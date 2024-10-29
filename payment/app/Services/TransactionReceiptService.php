<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use FPDF;

class TransactionReceiptService extends Fpdf
{
    const FONT_FAMILY = 'Helvetica';
    const FONT_FILE = 'helveticab.php';

    public function Header()
    {
        $this->AddFont(self::FONT_FAMILY, '', self::FONT_FILE);
        $this->SetFont(self::FONT_FAMILY, '', 14);
        $this->Image('https://res.cloudinary.com/zuberi-pay/image/upload/v1692183281/pf9meznc9azif2tusfei.png', 0, 0, 210);
        $this->Ln(20);
    }

    public function Footer()
    {
        $this->Image('https://res.cloudinary.com/zuberi-pay/image/upload/v1644581601/play-and-app-store_cldqjw.png', 24, 267, 95, 18);
    }

    public function BasicTable($data, $header = [])
    {
        foreach ($header as $col) {
            $this->Cell(40, 7, $col, 0);
        }
        $this->Ln(27);
        $i = 135;
        $counter = 0;
        $lineHeight = 20;
        foreach ($data as $header => $row) {
            $this->AddFont(self::FONT_FAMILY, '', self::FONT_FILE);
            $this->SetFont(self::FONT_FAMILY, '', 14);
            $this->Cell(80, 6, $header, 0);
            $this->AddFont(self::FONT_FAMILY, 'B', self::FONT_FILE);
            $this->SetFont(self::FONT_FAMILY, 'B', 14);
            $this->SetTextColor(35, 65, 78);
            if ($row === 'SUCCESS') {
                $this->SetTextColor(109, 180, 23);
            }
            if ($row === 'PENDING') {
                $this->SetTextColor(225, 180, 0);
            }
            if ($row === 'FAILED') {
                $this->SetTextColor(225, 60, 1);
            }
            if (preg_match("/^\+23320/", $row) || preg_match("/^\+23350/", $row)) {
                $this->SetX(110);
                $this->Image('https://res.cloudinary.com/zuberi-pay/image/upload/v1644581669/vodafone-modified_kkqqsy.png', 103, 122.1, 5, 5);
            }
            if (preg_match("/^\+23324/", $row) || preg_match("/^\+23354/", $row) || preg_match("/^\+23355/", $row) || preg_match("/^\+23359/", $row)) {
                $this->SetX(112);
                $this->Image('https://res.cloudinary.com/zuberi-pay/image/upload/v1644581668/mtn-logo_vy0cm7.png', 103, 120.8, 7, 7);
            }
            if (preg_match("/^\+23326/", $row) || preg_match("/^\+23327/", $row) || preg_match("/^\+23357/", $row) || preg_match("/^\+23356/", $row)) {
                $this->SetX(115);
                $this->Image('https://res.cloudinary.com/zuberi-pay/image/upload/v1644581670/airteltigo-logo_eprzbj.jpg', 103, 120.8, 10, 7);
            }
            $this->Cell(80, 6, $row, 0);
            $this->SetDrawColor(231, 231, 231);
            $this->Line(23, $i, 186, $i);
            $this->Ln($lineHeight);

            if ($counter < 5) {
                $i += 20;
            }
            $counter++;
        }
    }

    public function enquiry()
    {
        $firstOption = "If you have any questions or would like more information, please call our 24-hour Experience Poynt Team on ";
        $secondOption = " or send an email to";
        $contactLink = strval(env('TELEPHONE_SUPPORT_LINK'));
        $contact = strval(env('TELEPHONE_SUPPORT'));
        $email = "hello@itspoynt.com";
        $this->SetTextColor(35, 65, 78);
        $this->AddFont(self::FONT_FAMILY, '', self::FONT_FILE);
        $this->SetFont(self::FONT_FAMILY, '', 12);
        $this->Write(5, $firstOption);
        $this->SetFont(self::FONT_FAMILY, 'U', 12);
        $this->SetTextColor(64, 188, 254);
        $this->Write(5, "$contact", "$contactLink");
        $this->SetFont(self::FONT_FAMILY, '', 12);
        $this->SetTextColor(35, 65, 78);
        $this->Write(5, $secondOption);
        $this->SetFont(self::FONT_FAMILY, 'U', 12);
        $this->SetTextColor(64, 188, 254);
        $this->Write(5, "$email", "$email");
    }

    public static function createTransactionReceipt(Transaction $transaction): bool
    {
        $pdf = new TransactionReceiptService();
        $data = $pdf->getTransactionData($transaction);

        try {
            $pdf->AddPage();
            $pdf->title($data['Date'], $transaction);
            $pdf->getTransactionAmount($data['Amount']);
            unset($data['Date']);
            unset($data['Amount']);
            $pdf->BasicTable($data);
            $pdf->enquiry();
            $pdf->Output('D', 'transaction-receipt-' . $transaction->external_id . '.pdf');
            return true;
        } catch (Exception $exception) {
            report($exception);
        }
        return false;
    }

    public function getTransactionData(Transaction $transaction): array
    {
        return [
            'Type' => $transaction->payment->type,
            'Amount' => $transaction->amount,
            'Network' => $this->getNetworkName($transaction->r_switch),
            'Number' => $transaction->account_number,
            'Date' => $transaction->created_at,
        ];
    }

    private function getNetworkName(string $networkCode): string
    {
        return match (strtolower($networkCode)) {
            'vod' => 'Vodafone',
            'atl', 'tgi', 'atg' => 'AirtelTigo',
            default => 'MTN'
        };
    }

    public function getTransactionDate($date, $transaction)
    {
        $date = Carbon::parse($date->toDateString())->format('jS F, Y');
        $this->SetTextColor(18, 18, 18);
        $this->AddFont(self::FONT_FAMILY, 'B', self::FONT_FILE);
        $this->SetFont(self::FONT_FAMILY, 'B', 12);
        $this->SetFillColor(243, 243, 243);
        $this->RoundedRect(23, 66, 125, 10, 3, 'F');
        $this->SetX(25);
        $this->Cell(45, 12, $date, 0, 0, 'l');
        $this->AddFont(self::FONT_FAMILY, '', self::FONT_FILE);
        $this->SetFont(self::FONT_FAMILY, '', 11);
        $this->SetFillColor(135, 190, 201);
        $this->Cell(5, 0, '', 0, 0, 'l');
        $this->SetFillColor(135, 190, 201);
        $this->AddFont(self::FONT_FAMILY, '', self::FONT_FILE);
        $this->SetFont(self::FONT_FAMILY, '', 11);
        $this->Cell(50, 12, 'GENERATED FROM POYNT', 0, 0, 'l');
        if ($transaction->transactable_type === 'App\Models\Deduction') {
            $this->Image('https://res.cloudinary.com/zuberi-pay/image/upload/v1644581799/date-separator_fhm0il.png', 74, 68.3, 0.05, 5);
        } else {
            $this->Image('https://res.cloudinary.com/zuberi-pay/image/upload/v1644581799/date-separator_fhm0il.png', 71.4, 68.3, 0.05, 5);
        }
    }

    public function getTransactionAmount($amount)
    {
        $amount = 'GHS ' . $amount;
        $this->SetY(95);
        $this->AddFont(self::FONT_FAMILY, 'B', self::FONT_FILE);
        $this->SetFont(self::FONT_FAMILY, 'B', 30);
        $this->SetTextColor(35, 65, 78);
        $this->Cell(50, 12, $amount, 0, 0, 'l');
    }

    public function title($date, $transaction)
    {
        $this->Ln(20);
        $this->SetLeftMargin(22);
        $this->SetRightMargin(26);
        $this->AddFont(self::FONT_FAMILY, 'B', self::FONT_FILE);
        $this->SetFont(self::FONT_FAMILY, 'B', 20);
        $this->SetTextColor(35, 65, 78);
        $this->Cell(50, 12, 'TRANSACTION RECEIPT', 0, 0, 'l');
        $this->Image('https://res.cloudinary.com/zuberi-pay/image/upload/v1644581973/withdrawal-successful_rdggn2.png', 150, 45, 40, 40);
        $this->Ln(15);
        $this->getTransactionDate($date, $transaction);
    }

    public function RoundedRect($x, $y, $w, $h, $r, $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if ($style == 'F')
            $op = 'f';
        elseif ($style == 'FD' || $style == 'DF')
            $op = 'B';
        else
            $op = 'S';
        $MyArc = 4 / 3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m', ($x + $r) * $k, ($hp - $y) * $k));
        $xc = $x + $w - $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - $y) * $k));

        $this->_Arc($xc + $r * $MyArc, $yc - $r, $xc + $r, $yc - $r * $MyArc, $xc + $r, $yc);
        $xc = $x + $w - $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', ($x + $w) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc + $r, $yc + $r * $MyArc, $xc + $r * $MyArc, $yc + $r, $xc, $yc + $r);
        $xc = $x + $r;
        $yc = $y + $h - $r;
        $this->_out(sprintf('%.2F %.2F l', $xc * $k, ($hp - ($y + $h)) * $k));
        $this->_Arc($xc - $r * $MyArc, $yc + $r, $xc - $r, $yc + $r * $MyArc, $xc - $r, $yc);
        $xc = $x + $r;
        $yc = $y + $r;
        $this->_out(sprintf('%.2F %.2F l', ($x) * $k, ($hp - $yc) * $k));
        $this->_Arc($xc - $r, $yc - $r * $MyArc, $xc - $r * $MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    public function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1 * $this->k, ($h - $y1) * $this->k,
            $x2 * $this->k, ($h - $y2) * $this->k, $x3 * $this->k, ($h - $y3) * $this->k));
    }
}
