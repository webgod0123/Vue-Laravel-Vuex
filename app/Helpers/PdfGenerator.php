<?php 

namespace App\Helpers;

use Boolean;

use Carbon\Carbon;

trait PdfGenerator {

    public function generatePdf(array $input) {

        // "ContractorBulstat" => "123123123"
        // "ContractorName" => "Example Contractor Name"
        // "IndividualEIK" => "020202"
        // "IndividualNames" => "Example Individual Name"
        // "StartDate" => "1967-08-10"
        // "LastAmendDate" => "1967-08-11"
        // "EndDate" => "1967-08-12"
        // "Reason" => "01"
        // "TimeLimit" => "1967-08-13"
        // "EcoCode" => "String"
        // "ProfessionCode" => "01"
        // "Remuneration" => "800"
        // "ProfessionName" => "IT Support"
        // "EKATTECode" => "token"

        $filterName = !isset($input['only_active']) || Boolean::parse($input['only_active']) === true
            ? 'Действащи трудови договори' 
            : 'Всички трудови договори';

        $contracts = $input['contracts'];
        if(isset($input['date'])) {
            $date = Carbon::parse($input['date']);
        } else {
            $date = Carbon::now();
        }
        $coreSubstitutes = [
            'date' => $date->format('d.m.Y'),
            'egn' =>$input['egn'],
            'filterName' => $filterName,
            'time' => Carbon::now()->format('d.m.Y H:i')
        ];

        if(!empty($contracts)) {
            
            $template = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('template_yes.docx'));
            $template->setValues($coreSubstitutes);

            foreach($contracts as $contract) {
                $substitutes[]  = array_merge($coreSubstitutes,[
                    'bulstat' => $contract['contractor_bulstat'],
                    'companyName' => $contract['contractor_name'],
                    'names' => $contract['individual_names'],
                    'startDate' => Carbon::parse($contract['start_date'])->format('d.m.Y'),
                    'reason' => $contract['reason'],
                    'limit' => isset($contract['time_limit']) ? Carbon::parse($contract['time_limit'])->format('d.m.Y') : '',
                    'ecoCode' => $contract['eco_code'],
                    'profCode' => $contract['profession_code'],
                    'profName' => $contract['profession_name'],
                    'eccate' => isset($contract['EKATTE_code']) ? $contract['EKATTE_code'] : '',
                    'salary' => $contract['remuneration'],
                    'terminationNote' => ''
                ]);
            }

            $template->cloneRowAndSetValues('bulstat', $substitutes);

        } else {
            $template = new \PhpOffice\PhpWord\TemplateProcessor(storage_path('template_no.docx'));
            $template->setValues($coreSubstitutes);
        }

        $template->saveAs(storage_path('/temp.docx'));

        // load parsed template
        $content = \PhpOffice\PhpWord\IOFactory::load(storage_path('temp.docx')); 

        // set pdf converter
        $domPdfPath = base_path('vendor/dompdf/dompdf');
        \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');
      
        // save it into PDF file
        $fileName = 'result.pdf';
        $PDFWriter = \PhpOffice\PhpWord\IOFactory::createWriter($content,'PDF');
        $PDFWriter->save(storage_path($fileName)); 

        return $fileName;
    }
}