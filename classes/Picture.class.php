<?php

class Picture {

    private $wymiar = Array();

    public function __construct($a = r, $b = 2, $c = 3, $d = 4, $e = 5) {
        header("Content-type: image/png");
        $this->countsize($a, $b, $c, $d, $e);
    }

    private function countsize($a, $b, $c, $d, $e) {
        $this->wymiar[] = $b;
        $this->wymiar[] = $c;
        $this->wymiar[] = $d;
        $this->wymiar[] = $e;
        foreach ($this->wymiar as $key => $value) {
            if (strpos($value, '/')) {
                $temp = explode('/', $value);
                $this->wymiar[$key] = new Fraction($temp[0], $temp[1]);
            } else {
                $this->wymiar[$key] = new Fraction($value);
            }
        }
        if ($a == 'm') {
            $handle = ImageCreate(460, 50) or die("Cannot Create image");
            $bg_color = ImageColorAllocate($handle, 255, 255, 255);
            $txt_color = ImageColorAllocate($handle, 0, 0, 0);
            $text = '(' . $this->wymiar[1]->toString() . ' / ' . $this->wymiar[1]->toString() . ') = 1 ';
            imagettftext($handle, 18, 0, 0, 35, $txt_color, '../css/Roboto.ttf', $text);
        } elseif ($a == 'r') {
            $handle = ImageCreate(460, 50) or die("Cannot Create image");
            $bg_color = ImageColorAllocate($handle, 255, 255, 255);
            $txt_color = ImageColorAllocate($handle, 0, 0, 0);
            $c = new Fraction($this->wymiar[0]->getNumerator(), $this->wymiar[0]->getDenominator());
            $c->divide($this->wymiar[1]);
            $text = '(' . $this->wymiar[0]->toString() . ') / (' . $this->wymiar[1]->toString() . ') = ' . $c->toString();
            imagettftext($handle, 18, 0, 0, 35, $txt_color, '../css/Roboto.ttf', $text);
        } elseif ($a == 'c') {
            $handle = ImageCreate(460, 50) or die("Cannot Create image");
            $bg_color = ImageColorAllocate($handle, 255, 255, 255);
            $txt_color = ImageColorAllocate($handle, 0, 0, 0);
            $text = '(' . $this->wymiar[0]->toString() . ' * 0) = 0';
            imagettftext($handle, 18, 0, 0, 35, $txt_color, '../css/Roboto.ttf', $text);
        } elseif ($a == 'g') {
            $handle = ImageCreate(490, 50) or die("Cannot Create image");
            $bg_color = ImageColorAllocate($handle, 255, 255, 255);
            $txt_color = ImageColorAllocate($handle, 0, 0, 0);
            $s = new Fraction($this->wymiar[1]->getNumerator(), $this->wymiar[1]->getDenominator());
            $s->multiply($this->wymiar[2]);
            $s->divide($this->wymiar[3]);
            $text = $this->wymiar[0]->toString() . ' - (' . $this->wymiar[1]->toString() . ' * ' . $this->wymiar[2]->toString() . ') / (' . $this->wymiar[3]->toString() . ') = ';
            $this->wymiar[0]->substract($s);
            $text.=$this->wymiar[0]->toString();
            imagettftext($handle, 18, 0, 0, 35, $txt_color, '../css/Roboto.ttf', $text);
        }
        imagecolortransparent($handle, $bg_color);
        ImagePng($handle);
    }

}

?>