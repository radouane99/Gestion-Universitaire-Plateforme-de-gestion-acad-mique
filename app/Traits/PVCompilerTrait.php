<?php

namespace App\Traits;

use App\Models\Setting;
use Carbon\Carbon;

trait PVCompilerTrait
{
    /**
     * Compile students grades, semester/annual averages and academic decisions.
     */
    protected function compilePVData($students, $modules, $gradesGroupByStudent, $isAnnual, $semesters)
    {
        $compiled = [];

        $exams = \App\Models\Exam::whereIn('module_id', $modules->pluck('id'))
            ->where('type', 'Final')
            ->get()
            ->groupBy('module_id');

        foreach ($students as $student) {
            $studentGrades = $gradesGroupByStudent->get($student->id, collect());
            $studentGradesByModule = $studentGrades->keyBy('module_id');

            $modulesData = [];
            
            // Calculate grades and decisions for each module
            foreach ($modules as $module) {
                $g = $studentGradesByModule->get($module->id);

                $cc = null;
                if ($g) {
                    if ($g->cc1 !== null && $g->cc2 !== null) {
                        $cc = ($g->cc1 + $g->cc2) / 2;
                    } elseif ($g->cc1 !== null) {
                        $cc = $g->cc1;
                    } elseif ($g->cc2 !== null) {
                        $cc = $g->cc2;
                    }
                }

                $exam = $g ? $g->exam : null;
                $rattrapage = $g ? $g->rattrapage : null;
                $finalGrade = $g ? $g->final_grade : null;

                // Normal Session average
                $normalGrade = null;
                if ($cc !== null || $exam !== null) {
                    $normalGrade = (($cc ?? 0) * 0.4) + (($exam ?? 0) * 0.6);
                }

                // Module decision
                $decision = '';
                if ($finalGrade !== null) {
                    if ($finalGrade >= 10.0) {
                        $decision = ($rattrapage !== null) ? 'VAR' : 'V';
                    } else {
                        $decision = 'NV';
                    }
                } elseif ($g && $g->cc1 === null && $g->cc2 === null && $g->exam === null) {
                    $decision = 'ABS';
                }

                // If eligible for retake but note not set
                if ($finalGrade !== null && $finalGrade < 10.0 && $rattrapage === null) {
                    // Check if eligible
                    $isEligible = $student->retakeEligibilities()
                        ->where('exam_id', function ($query) use ($module) {
                            $query->select('id')->from('exams')->where('module_id', $module->id)->limit(1);
                        })
                        ->where('status', 'eligible')
                        ->exists();
                    if ($isEligible) {
                        $decision = 'R';
                    }
                }

                // Get exam/validation date
                $valDate = '-';
                if ($finalGrade !== null) {
                    $modExams = $exams->get($module->id) ?? collect();
                    $examRecord = $modExams->firstWhere('group_id', $student->group_id) 
                        ?? $modExams->first();
                        
                    if ($examRecord && $examRecord->date) {
                        $valDate = Carbon::parse($examRecord->date)->format('d/m/Y');
                    } elseif ($g && $g->updated_at) {
                        $valDate = $g->updated_at->format('d/m/Y');
                    } else {
                        $valDate = now()->format('d/m/Y');
                    }
                }

                $modulesData[$module->id] = [
                    'cc' => $cc,
                    'exam' => $exam,
                    'normal_grade' => $normalGrade,
                    'rattrapage' => $rattrapage,
                    'final_grade' => $finalGrade,
                    'decision' => $decision,
                    'val_date' => $valDate,
                ];
            }

            // Calculate Semesters averages and Decisions using the exact VBA rules
            $semestersData = [];
            foreach ($semesters as $sem) {
                $semModules = $modules->where('semester_id', $sem->id);
                $totalCoef = 0;
                $weightedSum = 0;
                $hasMissingGrades = false;

                $nbModulesNV = 0;
                $hasEliminatory = false;
                $failedModulesList = [];

                foreach ($semModules as $mod) {
                    $modData = $modulesData[$mod->id] ?? null;
                    $grade = $modData ? $modData['final_grade'] : null;
                    $dec = $modData ? $modData['decision'] : '';

                    if ($modData && $grade !== null) {
                        $weightedSum += $grade * ($mod->coefficient ?? 1);
                        $totalCoef += ($mod->coefficient ?? 1);

                        // CompterValeursCommunes logic: NV, NE, ABS, NC (and R for active retake)
                        if ($grade < 10.0 || in_array($dec, ['NV', 'NE', 'ABS', 'NC', 'R'])) {
                            $nbModulesNV++;
                            $failedModulesList[] = $mod->code ?? $mod->name;
                        }

                        // Eliminatory grade check (< 7)
                        if ($grade < 7.0) {
                            $hasEliminatory = true;
                        }
                    } else {
                        $hasMissingGrades = true;
                        $nbModulesNV++;
                        $failedModulesList[] = $mod->code ?? $mod->name;
                    }
                }

                $avg = ($totalCoef > 0) ? ($weightedSum / $totalCoef) : null;
                $decision = '';
                if ($avg !== null) {
                    // Exact Calcul_Decision_Semestre rule:
                    // If average < 10 OR failed modules > 2 OR any eliminatory grade (< 7)
                    if ($avg < 10.0 || $nbModulesNV > 2 || $hasEliminatory) {
                        $decision = 'NV';
                    } else {
                        $decision = 'V';
                    }
                }

                $semestersData[$sem->id] = [
                    'average' => $avg,
                    'decision' => $decision,
                    'has_missing' => $hasMissingGrades,
                    'nb_nv' => $nbModulesNV,
                    'has_eliminatory' => $hasEliminatory,
                    'failed_modules' => $failedModulesList,
                ];
            }

            // Calculate Annual Average and Decision
            $annualAverage = null;
            $annualDecision = '';
            $failedModulesAnnual = [];

            if ($isAnnual && $semesters->count() >= 2) {
                $semAverages = [];
                $allSemestersValidated = true;
                $semestersList = $semesters->values(); // S1 and S2

                $s1Data = isset($semestersList[0]) ? ($semestersData[$semestersList[0]->id] ?? null) : null;
                $s2Data = isset($semestersList[1]) ? ($semestersData[$semestersList[1]->id] ?? null) : null;

                foreach ($semesters as $sem) {
                    $semData = $semestersData[$sem->id] ?? null;
                    if ($semData) {
                        if ($semData['average'] !== null) {
                            $semAverages[] = $semData['average'];
                        }
                        if ($semData['decision'] !== 'V') {
                            $allSemestersValidated = false;
                        }
                        $failedModulesAnnual = array_merge($failedModulesAnnual, $semData['failed_modules']);
                    } else {
                        $allSemestersValidated = false;
                    }
                }

                if (count($semAverages) === $semesters->count()) {
                    $annualAverage = array_sum($semAverages) / count($semAverages);
                    
                    $studentLevel = $student->group->level ?? 1;

                    if ($allSemestersValidated) {
                        if ($studentLevel == 3) {
                            $annualDecision = 'Diplômé';
                        } else {
                            $annualDecision = 'Admis';
                        }
                    } else {
                        // Check if eligible for "Admis avec Crédit"
                        // Rule: S1 failed modules <= 2 AND S2 failed modules <= 2 (max 2 failed modules per semester)
                        $s1FailedCount = $s1Data ? $s1Data['nb_nv'] : 0;
                        $s2FailedCount = $s2Data ? $s2Data['nb_nv'] : 0;

                        if ($studentLevel != 3 && $s1FailedCount <= 2 && $s2FailedCount <= 2) {
                            $annualDecision = 'Admis avec Crédit';
                        } else {
                            $annualDecision = 'Ajourné';
                        }
                    }
                }
            } elseif (!$isAnnual && $semester) {
                // If single semester, annual calculation is just that semester
                $semData = $semestersData[$semester->id] ?? null;
                $annualAverage = $semData['average'] ?? null;
                $annualDecision = $semData['decision'] ?? '';
                $failedModulesAnnual = $semData['failed_modules'] ?? [];
            }

            // Highlight student if Conseil de Discipline is active or total absences >= threshold
            $settings = Setting::first();
            $disciplineThreshold = $settings?->absence_discipline_threshold ?? 120;
            $totalUnjustifiedAbsences = $student->absence_score;
            
            $isDisciplinary = ($totalUnjustifiedAbsences >= $disciplineThreshold) || $student->hasActiveDisciplineCase();

            // Observations text populating dynamic lists of failed modules
            $observations = '';
            if ($isAnnual) {
                if (($annualDecision === 'Ajourné' || $annualDecision === 'Admis avec Crédit') && !empty($failedModulesAnnual)) {
                    $observations = $annualDecision . ' (' . implode(' ; ', array_unique($failedModulesAnnual)) . ')';
                } elseif ($annualDecision === 'Admis' || $annualDecision === 'Diplômé') {
                    $observations = 'V';
                }
            } else {
                $semData = $semestersData[$semester->id] ?? null;
                if ($semData && $semData['decision'] === 'NV' && !empty($failedModulesAnnual)) {
                    $observations = 'NV (' . implode(' ; ', array_unique($failedModulesAnnual)) . ')';
                } elseif ($semData && $semData['decision'] === 'V') {
                    $observations = 'V';
                }
            }

            if ($isDisciplinary) {
                if ($student->hasActiveDisciplineCase()) {
                    $observations = '⚖️ Conseil de Discipline';
                } else {
                    $observations = '⚠️ ' . $totalUnjustifiedAbsences . 'h Absences';
                }
            }

            $compiled[$student->id] = [
                'student' => $student,
                'modules' => $modulesData,
                'semesters' => $semestersData,
                'annual_average' => $annualAverage,
                'annual_decision' => $annualDecision,
                'is_disciplinary' => $isDisciplinary,
                'observations' => $observations,
            ];
        }

        return $compiled;
    }
}
