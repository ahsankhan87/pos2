<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once dirname(__FILE__) . '/tfpdf/tfpdf.php';
class tpdf_f extends tFPDF
{
    function __construct()
    {
        parent::__construct();
    }

}
/*Author:ahsan khan */
/* End of file Pdf.php */
/* Location: ./application/libraries/Pdf.php */