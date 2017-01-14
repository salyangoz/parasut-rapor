<?php

namespace Salyangoz\ParasutRapor\Services;

use Carbon\Carbon;
use App;
use Salyangoz\ParasutRapor\Model\Sale;
use Illuminate\Support\Facades\Mail;

class Report
{

    private $parasutClient;
    private $sales  =   [];

    public function __construct($parasutClient)
    {
        $this->parasutClient    =   $parasutClient;
    }

    /**
     * Yeni bir rapor hazırlar mail ile gönderir
     * @return bool
     */
    public function create()
    {

        $period     =   $this->getPeriod();

        $startDate  =   Carbon::createFromTimestamp($period['start'])->format("Y-m-d");
        $endDate    =   Carbon::createFromTimestamp($period['end'])->format("Y-m-d");

        $sales      =   $this->getSales();

        $exportFile =   $this->createExportFile($sales, $startDate." - ". $endDate. " arası satış raporu");

        if($exportFile) {

            $this->sendEmail($exportFile, $startDate, $endDate);

        }

        return true;

    }

    /**
     * Oluşan raporun mailini iletir
     * @param $filePath
     * @param $startDate
     * @param $endDate
     * @return bool
     */
    private function sendEmail($filePath, $startDate, $endDate) {

        $mailview   =   'parasut-rapor::emails.report';

        Mail::send($mailview, ["startDate"=>$startDate,"endDate"=>$endDate],

            function ($m) use ($filePath, $startDate, $endDate)
            {
                $m->from(config('parasut-rapor.mail.from_email'), config('parasut-rapor.mail.from_name'));

                $m->to(explode(",",config('parasut-rapor.mail.to_email')));

                if(config('parasut-rapor.mail.cc_email')) {

                    $m->cc(explode(",",config('parasut-rapor.mail.cc_email')));

                }

                $m->subject("Paraşüt {$startDate} - {$endDate} arası aylık satış raporu");
                $m->attach($filePath);
            });

        return true;

    }

    /**
     * Satış dizisinden export dosya üretir ve servera kaydeder.
     * @param $sales
     * @param $fileName
     * @return bool
     */
    private function createExportFile($sales, $fileName)
    {
        if(count($sales) == 0) {
            return false;
        }

        $excel = App::make('excel');

        $file  = $excel->create($fileName, function($excel) use ($sales) {

            $excel->setTitle('Paraşüt Satış Raporu');

            $excel->sheet('Rapor', function($sheet)  use ($sales) {

                $sheet->appendRow(["Fatura ID", "Fatura Numarası", "Fatura Açıklaması",
                    "Müşteri Adı", "Müşteri Email", "Müşteri Adres", "Müşteri Vergi Dairesi", "Müşteri Vergi Numarası / TC",
                    "Düzenlenme Tarihi", "Ödenme Durumu", "Vergisiz Toplam", "Vergi Tutarı", "Toplam"]);

                foreach ($sales as $sale) {

                    $sheet->appendRow([
                        $sale->sale_id,
                        $sale->invoice_serial_number,
                        $sale->invoice_description,
                        $sale->customer_name,
                        $sale->customer_email,
                        $sale->customer_address,
                        $sale->customer_tax_office,
                        $sale->customer_tax_number,
                        $sale->issue_date,
                        $sale->payment_status,
                        $sale->gross_total,
                        $sale->vat,
                        $sale->total
                    ]);

                }

            });

        })->store('xls', false, true);

        return $file["full"];
    }

    /**
     * Ayarlanan periyod için başlangıç ve bitiş timestamplerini üretir.
     * @return array
     */
    private function getPeriod()
    {

        $period             =   config('parasut-rapor.report.period','monthly');

        if($period  == 'monthly') {

            $lastMonthStart =   Carbon::parse('first day of last month');
            $startFormatted =   $lastMonthStart->format("Y-m-d");
            $start          =   Carbon::parse($startFormatted)->timestamp;
            $end            =   Carbon::parse('last day of last month')->setTime(23, 59, 59)->timestamp;

        } elseif($period  == 'weekly') {

            $previous_week = strtotime("-1 week +1 day");

            $start  = strtotime("last monday midnight",$previous_week);
            $end    = strtotime("next sunday",$start);

            $startFormatted =   date("Y-m-d",$start);

        } else {

            $lastStart      =   Carbon::parse('yesterday')->setTime(0,0);
            $startFormatted =   $lastStart->format("Y-m-d");
            $start          =   $lastStart->timestamp;
            $end            =   Carbon::parse('today')->setTime(0,0)->timestamp - 1;

        }

        return [
            'start'          => $start,
            'start_formatted'=> $startFormatted,
            'end'            => $end
        ];

    }

    /**
     * Konfigurasyonda ayarlanan aralığa göre siparişleri getirir.
     * @param int $page
     * @return array
     */
    private function getSales($page = 1)
    {

        $period =   $this->getPeriod();

        $sales  =   $this->parasutClient->make('sale')->get($page, 100, $period['start_formatted']);

        foreach ((array)$sales['items'] as $sale) {

            $issueTimestamp         =   Carbon::createFromFormat("Y-m-d",$sale['issue_date'])->timestamp;

            if($sale['item_type'] == 'invoice' && $issueTimestamp >=  $period['start']
                && $issueTimestamp <=  $period['end'] &&
                starts_with($sale['invoice_no'],explode(",",config('parasut-rapor.report.invoice_prefix')))) {

                $saleModel  =   new Sale();

                $saleModel->sale_id              =   $sale['id'];
                $saleModel->customer_name        =   $sale['contact']['name'];
                $saleModel->invoice_description  =   $sale['description'];
                $saleModel->invoice_serial_number=   $sale['invoice_no'];
                $saleModel->issue_date           =   $sale['issue_date'];
                $saleModel->total                =   $sale['net_total'];
                $saleModel->payment_status       =   trans("parasut-rapor::report.".$sale['payment_status']);
                $saleModel->customer_address     =   $sale['contact']['address']['address'] . " ". $sale['contact']['district']. "/ ". $sale['contact']['city'] ;
                $saleModel->customer_email       =   $sale['contact']['email'];
                $saleModel->customer_tax_number  =   " ".$sale['contact']['tax_number'];
                $saleModel->customer_tax_office  =   $sale['contact']['tax_office'];
                $saleModel->gross_total          =   $sale['gross_total'];
                $saleModel->vat                  =   $sale['total_vat'];

                $this->sales[]                   =  $saleModel;

            }

        }

        if($sales['meta']['page_count'] > $page) {

            $page = $page + 1;

            return $this->getSales($page);

        }

        return $this->sales;

    }

}