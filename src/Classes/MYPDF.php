<?php


namespace App\Classes;


class MYPDF extends \TCPDF
{
    //Page header
    public function Header() {
        // Logo
        $image_file = K_PATH_IMAGES.'logo-01.jpg';
        $this->Image($image_file, 20, 4, 60, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
//        $this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-35);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Page number
        $this->Cell(0, 30, 'Address: Symphony Hotel, salmiya, basement floor, shop 33', 0, 0, 'C', 0, '', 1, false, 'T', 'M');
        $this->SetY(-30);
        $this->Cell(0, 30, 'Email: info@shineaurora.com', 0, 0, 'C', 0, '', 1, false, 'T', 'M');
        $this->SetY(-25);
        $this->Cell(0, 30, 'Mobile: +965 50266444', 0, 0, 'C', 0, '', 1, false, 'T', 'M');
    }
}