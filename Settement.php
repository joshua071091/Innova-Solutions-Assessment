<?php
function settleBills($bills) {
    $balanceDetails = [];
    
    foreach ($bills as $perbill) {
        $totalBillAmount = array_sum($perbill);
        $noofContributor = count($perbill);
        $splitShare = $totalBillAmount / $noofContributor;

        foreach ($perbill as $person => $amountContributed) {
            if (!isset($balanceDetails[$person])) {
                $balanceDetails[$person] = 0;
            }
            $balanceDetails[$person] += $amountContributed - $splitShare;
        }
        
        
    }

    $amountcredit = [];
    $amountdebit = [];
    
    foreach ($balanceDetails as $person => $balance) {
        $balance = round($balance);
        if ($balance > 0) {
            $amountcredit[] = ['person' => $person, 'amount' => $balance];
        } elseif ($balance < 0) {
            $amountdebit[] = ['person' => $person, 'amount' => -$balance];
        }
    }
    
    $settlements = [];

    while (!empty($amountdebit) && !empty($amountcredit)) {
        $debtor = array_pop($amountdebit);
        $creditor = array_pop($amountcredit);

        $settlementAmount = min($debtor['amount'], $creditor['amount']);

        $settlements[] = [
            "from" => $debtor['person'],
            "to" => $creditor['person'],
            "amount" => $settlementAmount
        ];

        if ($debtor['amount'] > $settlementAmount) {
            $debtor['amount'] -= $settlementAmount;
            $amountdebit[] = $debtor;
        }
        if ($creditor['amount'] > $settlementAmount) {
            $creditor['amount'] -= $settlementAmount;
            $amountcredit[] = $creditor;
        }
    }

    return $settlements;
}

$bills = [
    ['A' => 2000, 'B' => 200, 'C' => 1000],
    ['A' => 1500, 'B' => 0, 'C' => 0, 'D' => 1300],
    ['B' => 100, 'C' => 0]
];

$settlements = settleBills($bills);

print_r($settlements);
?>
