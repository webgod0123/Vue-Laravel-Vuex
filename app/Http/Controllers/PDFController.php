<?php

namespace App\Http\Controllers;

use App\Helpers\PdfGenerator;

use Illuminate\Http\Request;

class PDFController extends Controller
{
    use PdfGenerator;
    
    public function contract(Request $POST)
    {
        $fileName = $this->generatePdf($POST->all());

        return $fileName;
    }

    public function fetchFile($fileName)
    {
        $path = storage_path($fileName);

        return response()->file($path);
    }
}
