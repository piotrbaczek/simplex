<?php

/** Outputs Picture with Gauss-Jordan transformation of current SimpleMatrix element
 * @author Piotr Gołasz <pgolasz@gmail.com>
 * @version 1.0
 * @see DivisionCoefficient
 * @deprecated
 */
class Picture
{

    private $dimension = Array();
    private $fontFilePath;

    /**
     *
     * @param String $a
     * @param Integer $b
     * @param Integer $c
     * @param Integer $d
     * @param Integer $e
     */
    public function __construct($a = 'r', $b = 2, $c = 3, $d = 4, $e = 5)
    {
        $this->fontFilePath = __DIR__ . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'Roboto.ttf';
        $this->countsize($a, $b, $c, $d, $e);
    }

    private function countsize($a, $b, $c, $d, $e)
    {
        $this->dimension[] = $b;
        $this->dimension[] = $c;
        $this->dimension[] = $d;
        $this->dimension[] = $e;
        if ($a != 'dc') {
            foreach ($this->dimension as $key => $value) {
                if (strpos($value, '/')) {
                    $temp = explode('/', $value);
                    $this->dimension[$key] = new Fraction($temp[0], $temp[1]);
                } else {
                    $this->dimension[$key] = new Fraction($value);
                }
            }
        }

        if ($a == 'm') {
            $handle = ImageCreate(460, 50) or die("Cannot Create image");
            $bg_color = ImageColorAllocate($handle, 255, 255, 255);
            $txt_color = ImageColorAllocate($handle, 0, 0, 0);
            $text = '(' . $this->dimension[1] . ' / ' . $this->dimension[1] . ') = 1 ';
            imagettftext($handle, 18, 0, 0, 35, $txt_color, $this->fontFilePath, $text);
        } elseif ($a == 'r') {
            $handle = ImageCreate(460, 50) or die("Cannot Create image");
            $bg_color = ImageColorAllocate($handle, 255, 255, 255);
            $txt_color = ImageColorAllocate($handle, 0, 0, 0);
            $c = new Fraction($this->dimension[0]->getNumerator(), $this->dimension[0]->getDenominator());
            $c->divide($this->dimension[1]);
            $text = '(' . $this->dimension[0] . ') / (' . $this->dimension[1] . ') = ' . $c;
            imagettftext($handle, 18, 0, 0, 35, $txt_color, $this->fontFilePath, $text);
        } elseif ($a == 'c') {
            $handle = ImageCreate(460, 50) or die("Cannot Create image");
            $bg_color = ImageColorAllocate($handle, 255, 255, 255);
            $txt_color = ImageColorAllocate($handle, 0, 0, 0);
            $text = '(' . $this->dimension[0] . ' * 0) = 0';
            imagettftext($handle, 18, 0, 0, 35, $txt_color, $this->fontFilePath, $text);
        } elseif ($a == 'g') {
            $handle = ImageCreate(490, 50) or die("Cannot Create image");
            $bg_color = ImageColorAllocate($handle, 255, 255, 255);
            $txt_color = ImageColorAllocate($handle, 0, 0, 0);
            $s = new Fraction($this->dimension[1]->getNumerator(), $this->dimension[1]->getDenominator());
            $s->multiply($this->dimension[2]);
            $s->divide($this->dimension[3]);
            $text = $this->dimension[0] . ' - (' . $this->dimension[1] . ' * ' . $this->dimension[2] . ') / (' . $this->dimension[3] . ') = ';
            $this->dimension[0]->substract($s);
            $text .= $this->dimension[0];
            imagettftext($handle, 18, 0, 0, 35, $txt_color, $this->fontFilePath, $text);
        } elseif ($a == 'dc') {
            $handle = ImageCreate(490, 50) or die("Cannot Create image");
            $bg_color = ImageColorAllocate($handle, 255, 255, 255);
            $txt_color = ImageColorAllocate($handle, 0, 0, 0);
            if ($this->dimension[0] == '-') {
                $text = '' . $this->dimension[0] . ' / ' . $this->dimension[1];
            } else {
                $text = '' . $this->dimension[0] . ' / ' . $this->dimension[1] . ' = ' . $this->dimension[2];
            }
            imagettftext($handle, 18, 0, 0, 35, $txt_color, $this->fontFilePath, $text);
        }
        imagecolortransparent($handle, $bg_color);
        ImagePng($handle);
    }

}

?>