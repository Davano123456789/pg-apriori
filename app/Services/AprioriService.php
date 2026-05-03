<?php

namespace App\Services;

class AprioriService
{
    private $transactions = [];
    private $minSupportCount;
    private $minConfidence;
    private $totalTransactions;
    private $frequentItemsets = [];
    private $itemNames = [];

    public function __construct($transactions, $minSupportPercent, $minConfidence, $itemNames = [])
    {
        $this->transactions = $transactions;
        $this->totalTransactions = count($transactions);
        $this->minSupportCount = ($minSupportPercent / 100) * $this->totalTransactions;
        $this->minConfidence = $minConfidence / 100;
        $this->itemNames = $itemNames;
    }

    private function getItemsetKey($items)
    {
        $items = array_map(fn($val) => trim((string)$val), $items);
        sort($items);
        return implode(',', $items);
    }

    public function process()
    {
        $stepByStep = [];

        // 1. Generate frequent 1-itemsets
        $candidates = [];
        foreach ($this->transactions as $transaction) {
            foreach ($transaction as $item) {
                $item = trim((string)$item);
                $candidates[$item] = ($candidates[$item] ?? 0) + 1;
            }
        }

        $allCandidates = [];
        $currentFrequentItemsets = [];
        foreach ($candidates as $item => $count) {
            $support = ($count / $this->totalTransactions) * 100;
            $isFrequent = $count >= $this->minSupportCount;
            
            $allCandidates[] = [
                'items' => [$item],
                'count' => $count,
                'support' => $support,
                'is_frequent' => $isFrequent
            ];

            if ($isFrequent) {
                $currentFrequentItemsets[$this->getItemsetKey([$item])] = $count;
            }
        }

        // Sort candidates by code for neatness (Natural Sort)
        usort($allCandidates, fn($a, $b) => strnatcmp($a['items'][0], $b['items'][0]));

        $this->frequentItemsets[1] = $currentFrequentItemsets;
        $stepByStep[1] = [
            'candidates' => $allCandidates,
            'frequent' => $this->formatItemsets($currentFrequentItemsets)
        ];

        // 2. Generate frequent k-itemsets (k > 1)
        $k = 2;
        while (true) {
            // We need at least 2 frequent itemsets from previous step to form a new candidate in next step
            if (count($this->frequentItemsets[$k - 1]) < ($k == 2 ? 2 : 1)) break;

            $candidateSets = $this->generateCandidates(array_keys($this->frequentItemsets[$k - 1]), $k);
            if (empty($candidateSets)) break;

            $counts = $this->countSupport($candidateSets);
            
            $allCandidatesK = [];
            $frequent = [];
            foreach ($counts as $itemKey => $count) {
                $support = ($count / $this->totalTransactions) * 100;
                $isFrequent = $count >= $this->minSupportCount;
                
                $allCandidatesK[] = [
                    'items' => explode(',', $itemKey),
                    'count' => $count,
                    'support' => $support,
                    'is_frequent' => $isFrequent
                ];

                if ($isFrequent) {
                    $frequent[$itemKey] = $count;
                }
            }

            // Sort k-itemset candidates naturally by the first item
            usort($allCandidatesK, function($a, $b) {
                return strnatcmp(implode(',', $a['items']), implode(',', $b['items']));
            });

            // Always add the step results even if none are frequent
            $stepByStep[$k] = [
                'candidates' => $allCandidatesK,
                'frequent' => $this->formatItemsets($frequent)
            ];

            // If no frequent itemsets found, we stop AFTER showing this step's candidates
            if (empty($frequent)) break;

            $this->frequentItemsets[$k] = $frequent;
            $k++;

            // Safety break to prevent infinite loops (max 10 itemsets)
            if ($k > 10) break;
        }

        // 3. Generate Association Rules
        $rules = $this->generateRules();

        return [
            'step_by_step' => $stepByStep,
            'rules' => $rules,
            'total_transactions' => $this->totalTransactions
        ];
    }

    private function generateCandidates($prevKeys, $k)
    {
        $candidates = [];
        $itemsets = array_map(fn($key) => explode(',', $key), $prevKeys);
        $n = count($itemsets);

        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $set1 = $itemsets[$i];
                $set2 = $itemsets[$j];

                // Join step: join sets if first k-2 elements are same
                $joinable = true;
                for ($m = 0; $m < $k - 2; $m++) {
                    if ($set1[$m] !== $set2[$m]) {
                        $joinable = false;
                        break;
                    }
                }

                if ($joinable) {
                    $newCandidate = array_unique(array_merge($set1, $set2));
                    $itemKey = $this->getItemsetKey($newCandidate);
                    if (count(explode(',', $itemKey)) == $k) {
                        $candidates[$itemKey] = 0;
                    }
                }
            }
        }
        return $candidates;
    }

    private function countSupport($candidates)
    {
        foreach ($this->transactions as $transaction) {
            $transaction = array_map(fn($val) => trim((string)$val), $transaction);
            foreach ($candidates as $itemKey => $count) {
                $items = explode(',', $itemKey);
                if (count(array_intersect($items, $transaction)) == count($items)) {
                    $candidates[$itemKey]++;
                }
            }
        }
        return $candidates;
    }

    private function generateRules()
    {
        $rules = [];
        // Rules can only be generated from itemsets with k >= 2
        foreach ($this->frequentItemsets as $k => $itemsets) {
            if ($k < 2) continue;

            foreach ($itemsets as $itemKey => $supportCount) {
                $itemset = explode(',', $itemKey);
                $subsets = $this->getPowerSet($itemset);

                foreach ($subsets as $antecedent) {
                    if (empty($antecedent) || count($antecedent) == count($itemset)) continue;

                    $antecedentKey = $this->getItemsetKey($antecedent);
                    $consequent = array_values(array_diff($itemset, $antecedent));
                    $consequentKey = $this->getItemsetKey($consequent);
                    
                    // Support(A union B)
                    $supportCountAB = $supportCount;
                    
                    // Support(A)
                    $supportCountA = $this->frequentItemsets[count($antecedent)][$antecedentKey] ?? 0;

                    if ($supportCountA > 0) {
                        $confidence = $supportCountAB / $supportCountA;
                        if ($confidence >= $this->minConfidence) {
                            $supportPercent = ($supportCountAB / $this->totalTransactions) * 100;
                            
                            $rules[] = [
                                'antecedent' => $antecedent,
                                'consequent' => $consequent,
                                'support' => $supportPercent,
                                'confidence' => $confidence * 100,
                                'confidence_ratio' => $supportCountAB . '/' . $supportCountA,
                                'antecedent_names' => array_map(fn($code) => $this->itemNames[$code] ?? $code, $antecedent),
                                'consequent_names' => array_map(fn($code) => $this->itemNames[$code] ?? $code, $consequent),
                            ];
                        }
                    }
                }
            }
        }
        return $rules;
    }

    private function getPowerSet($array)
    {
        $results = [[]];
        foreach ($array as $element) {
            foreach ($results as $combination) {
                $results[] = array_merge($combination, [$element]);
            }
        }
        return $results;
    }

    private function formatItemsets($itemsets)
    {
        $formatted = [];
        foreach ($itemsets as $itemKey => $count) {
            $formatted[] = [
                'items' => explode(',', $itemKey),
                'count' => $count,
                'support' => ($count / $this->totalTransactions) * 100
            ];
        }

        // Sort naturally
        usort($formatted, function($a, $b) {
            return strnatcmp(implode(',', $a['items']), implode(',', $b['items']));
        });

        return $formatted;
    }
}
