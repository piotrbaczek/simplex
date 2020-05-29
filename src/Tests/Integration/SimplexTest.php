<?php

namespace pbaczek\simplex\Tests\Integration;

use Exception;
use pbaczek\simplex\Fraction;
use pbaczek\simplex\Simplex\Sign;
use pbaczek\simplex\Simplex\Solver\Dantzig;
use PHPUnit\Framework\TestCase;
use Simplex;
use TextareaProcesser;

/**
 * Class SimplexTest
 * @package pbaczek\simplex\Tests\Integration
 */
class SimplexTest extends TestCase
{
//    /**
//     * Test simple functionality
//     * @return void
//     *
//     */
//    public function testBasicFunctionality(): void
//    {
//        // max 2x1+6x2
//        //2x1+5x2<=30
//        //2x1+3x2<=26
//        //0x1+3x2<=15
//
//        $variables = [
//            [
//                new Fraction(2), new Fraction(5),
//            ],
//            [
//                new Fraction(2), new Fraction(3)
//            ],
//            [
//                new Fraction(0), new Fraction(3)
//            ],
//        ];
//        $signs = new Simplex\SignCollection([new Sign\LessOrEqual(), new Sign\LessOrEqual(), new Sign\LessOrEqual()]);
//        $boundaries = new Simplex\FractionCollection([new Fraction(30), new Fraction(26), new Fraction(15)]);
//        $targetFunction = new Simplex\FractionCollection([new Fraction(2), new Fraction(6)]);
//
//        $solver = new Dantzig($variables, $signs, $boundaries, $targetFunction);
//
//        $simplex = new Simplex($solver);
//        $simplex->run();
//    }

    public function testOldSimplex()
    {
        require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Signs.class.php';
        require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Point.class.php';
        require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Fraction.class.php';
        require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Simplex.class.php';
        require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'TextareaProcesser.class.php';
        require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'SimplexTableau.class.php';
        require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'DivisionCoefficient.class.php';

        $css = '<style>table.result td.mainelement {
	color:white;
	background-color:red;
	text-align:center;
	width:45px;
}</style>';

        $textarea = '2x1+5x2<=30
2x1+3x2<=26
0x1+3x2<=15';
        $targetfunction = '2x1+6x2';
        $function = 'true';
        $gomorry = 'false';

        $tp = new TextareaProcesser(
            $textarea, $targetfunction, $function, $gomorry
        );
        try {
            if ($tp->isCorrect()) {
                $simplex = new Simplex($tp->getVariables(), $tp->getBoundaries(), $tp->getSigns(), $tp->getTargetfunction(), $tp->getMaxMin(), $tp->getGomorry());

                file_put_contents('result.html', $css . $simplex->printSolution() . $simplex->printValuePair() . $simplex->printResult());
            } else {
                $json[0] = -2;
                $json[3] = TextareaProcesser::errormessage('Puste dane lub złe dane. Proszę poprawić treść wpisanego zadania.');
            }
        } catch (Exception $e) {
            $json[0] = -2;
            $json[3] = TextareaProcesser::errormessage($e->getMessage());
        }
    }
}