<?php
/**
 * Pop PHP Framework (http://www.popphp.org/)
 *
 * @link       https://github.com/popphp/popphp2
 * @category   Pop
 * @package    Pop_Pdf
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2014 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 */

/**
 * @namespace
 */
namespace Pop\Pdf;

/**
 * Abstract Pdf effect class
 *
 * @category   Pop
 * @package    Pop_Pdf
 * @author     Nick Sagona, III <dev@nolainteractive.com>
 * @copyright  Copyright (c) 2009-2014 NOLA Interactive, LLC. (http://www.nolainteractive.com)
 * @license    http://www.popphp.org/license     New BSD License
 * @version    2.0.0a
 */
abstract class AbstractEffect
{

    /**
     * PDF object
     * @var Pdf
     */
    protected $pdf = null;

    /**
     * Fill color of the document
     * @var array
     */
    protected $fillColor = null;

    /**
     * Stroke color of the document
     * @var array
     */
    protected $strokeColor = null;

    /**
     * Stroke width
     * @var int
     */
    protected $strokeWidth = 0;

    /**
     * Stroke dash length
     * @var int
     */
    protected $strokeDashLength = null;

    /**
     * Stroke dash gap
     * @var int
     */
    protected $strokeDashGap = null;

    /**
     * Constructor
     *
     * Instantiate a PDF effect object
     *
     * @param  Pdf $pdf
     * @return AbstractEffect
     */
    public function __construct(Pdf $pdf = null)
    {
        if (null !== $pdf) {
            $this->setPdf($pdf);
        }
    }

    /**
     * Get the PDF object
     *
     * @return Pdf
     */
    public function getPdf()
    {
        return $this->pdf;
    }


    /**
     * Get fill color
     *
     * @return mixed
     */
    public function getFillColor()
    {
        return $this->fillColor;
    }

    /**
     * Get stroke color
     *
     * @return array
     */
    public function getStrokeColor()
    {
        return $this->strokeColor;
    }

    /**
     * Get stroke width
     *
     * @return int
     */
    public function getStrokeWidth()
    {
        return $this->strokeWidth;
    }

    /**
     * Get stroke dash length
     *
     * @return int
     */
    public function getStrokeDashLength()
    {
        return $this->strokeDashLength;
    }

    /**
     * Get stroke dash gap
     *
     * @return int
     */
    public function getStrokeDashGap()
    {
        return $this->strokeDashGap;
    }

    /**
     * Set the PDF object
     *
     * @param  Pdf
     * @return AbstractEffect
     */
    public function setPdf(Pdf $pdf)
    {
        $this->pdf = $pdf;
        return $this;
    }

    /**
     * Method to set the fill color of objects and text in the PDF.
     *
     * @param  int $r
     * @param  int $g
     * @param  int $b
     * @return Pdf
     */
    public function setFillColor($r = 0, $g = 0, $b = 0)
    {
        $this->fillColor = [(int)$r, (int)$g, (int)$b];

        $coIndex = $this->getContentObject();
        $this->objects[$coIndex]->setStream("\n" . $this->convertColor((int)$r) . " " .
            $this->convertColor((int)$g) . " " . $this->convertColor((int)$b) . " rg\n");

        return $this;
    }

    /**
     * Method to set the stroke color of paths in the PDF.
     *
     * @param  int $r
     * @param  int $g
     * @param  int $b
     * @return Pdf
     */
    public function setStrokeColor($r = 0, $g = 0, $b = 0)
    {
        $this->strokeColor = [(int)$r, (int)$g, (int)$b];

        $coIndex = $this->getContentObject();
        $this->objects[$coIndex]->setStream("\n" . $this->convertColor((int)$r) . " " .
            $this->convertColor((int)$g) . " " . $this->convertColor((int)$b) . " RG\n");

        return $this;
    }

    /**
     * Method to set the width and dash properties of paths in the PDF.
     *
     * @param  int $w
     * @param  int $dashLength
     * @param  int $dashGap
     * @return Pdf
     */
    public function setStrokeWidth($w = null, $dashLength = null, $dashGap = null)
    {
        if ((null === $w) || ($w == false) || ($w == 0)) {
            $this->stroke           = false;
            $this->strokeWidth      = null;
            $this->strokeDashLength = null;
            $this->strokeDashGap    = null;
        } else {
            $this->stroke           = true;
            $this->strokeWidth      = $w;
            $this->strokeDashLength = $dashLength;
            $this->strokeDashGap    = $dashGap;

            // Set stroke to the $w argument, or else default it to 1pt.
            $newString = "\n{$w} w\n";

            // Set the dash properties of the stroke, or else default it to a solid line.
            $newString .= ((null !== $dashLength) && (null !== $dashGap)) ? "[{$dashLength} {$dashGap}] 0 d\n" : "[] 0 d\n";

            $coIndex = $this->getContentObject();
            $this->objects[$coIndex]->setStream($newString);
        }

        return $this;
    }

}