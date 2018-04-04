<?php
/*
 * This file is part of the WHAAM project funded with support from the European Commission.
 *
 * Reference project number: 531244-LLP-2012-IT-KA3MP
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @author Gianluca Merlo
 */
namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Util\BehavioralDataChart;

use Ob\HighchartsBundle\Highcharts\Highchart;
use Symfony\Component\Translation\Translator;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessment;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentIntervention;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Util\BehavioralDataChart\BehavioralDataGenerator;
use Zend\Json\Expr;

class BehavioralChartGenerator {
    private $behavioralDataGenerator;
    private $translator;

    public function __construct(BehavioralDataGenerator $behavioralDataGenerator, Translator $translator) {
        $this->behavioralDataGenerator = $behavioralDataGenerator;
        $this->translator = $translator;
    }

    public function generateScatterPlotForAllAssessmentPhases(ChildBehaviorAssessment $assessment) {
        if($assessment->getBaselines()->count() == 0) {
            throw new \Exception('At least a baseline is required to generate the chart');
        }

        $behavior = (string) $assessment->getChildBehavior();

        $observationType = $assessment->getBaselines()->first()->getObservationType();

        $yAxisTitle = $this->getYAxisTitle($observationType);

        $series = array();

        $startingInterventionSessionNumber = 1;

        foreach($assessment->getBaselines() as $baseline) {
            $baselineDataSet = ($observationType == 'duration') ?
                $this->behavioralDataGenerator->generatePartialIntervalDataSet(
                    $baseline->getObservationSessions(),
                    $startingInterventionSessionNumber
                )
                : $this->behavioralDataGenerator->generateFrequencyDataSet(
                    $baseline->getObservationSessions(),
                    $startingInterventionSessionNumber
                );

            $startingInterventionSessionNumber += count($baselineDataSet);

            $series[] = array("name" => 'Baseline', "data" => $baselineDataSet);

            $intervention = $baseline->getIntervention();

            if($intervention) {
                $interventionDataSet = ($observationType == 'duration') ?
                    $this->behavioralDataGenerator->generatePartialIntervalDataSet(
                        $intervention->getObservationSessions(),
                        $startingInterventionSessionNumber
                    )
                    : $this->behavioralDataGenerator->generateFrequencyDataSet(
                        $intervention->getObservationSessions(),
                        $startingInterventionSessionNumber
                    );

                $series[] = array("name" => 'Intervention', "data" => $interventionDataSet);

                $startingInterventionSessionNumber += count($interventionDataSet);
            }
        }

        return $this->generateScatterPlot(
            $behavior,
            $series,
            'linechart',
            $this->translator->trans('session', array(), 'chart'),
            $yAxisTitle
        );
    }

    public function generateScatterPlotForAssessmentPhase($phase) {
        if(!($phase instanceof ChildBehaviorAssessmentBaseline) && !($phase instanceof ChildBehaviorAssessmentIntervention)) {
            throw new \Exception('Wrong phase object');
        }

        $phaseName = ($phase instanceof ChildBehaviorAssessmentBaseline) ? 'baseline' : 'intervention';

        $behavior = ($phaseName == 'baseline') ?
            (string) $phase->getChildBehaviorAssessment()->getChildBehavior() :
            (string) $phase->getChildBehaviorAssessmentBaseline()->getChildBehaviorAssessment()->getChildBehavior();

        $observationType = ($phaseName == 'baseline') ?
            $phase->getObservationType() :
            $phase->getChildBehaviorAssessmentBaseline()->getObservationType();

        $yAxisTitle = $this->getYAxisTitle($observationType);

        $dataSet = ($observationType == 'frequency') ?
            $this->behavioralDataGenerator->generateFrequencyDataSet($phase->getObservationSessions())
            : $this->behavioralDataGenerator->generateDurationDataSet($phase->getObservationSessions());

        $series = array(
            array("name" => ucfirst($phaseName), "data" => $dataSet),
        );

        return $this->generateScatterPlot(
            $behavior,
            $series,
            'linechart',
            $this->translator->trans('session', array(), 'chart'),
            $yAxisTitle
        );
    }

    private function generatePlotLines(array $series)
    {
        $plotLines = array();

        $numberOfSeries = count($series);

        for($i=0; $i<=$numberOfSeries-1; $i++) {

            $nextSeriesIndex = $i + 1;

            if(array_key_exists($nextSeriesIndex, $series)) {

                $numberOfObservationFirstDataSet = array_pop($series[$i]['data']);
                $numberOfObservationFirstDataSet = $numberOfObservationFirstDataSet['x'];

                $xCoordinate = ($numberOfObservationFirstDataSet + $numberOfObservationFirstDataSet + 1) / 2;

                $plotLines[] = array(
                    'color' => 'red',
                    'dashStyle' => 'longdashdot',
                    'value' => $xCoordinate,
                    'width' => '2'
                );
            }
        }

        return $plotLines;
    }

    private function generateScatterPlot($title, array $series, $divId, $xAxisTitle, $yAxisTitle) {
        $xAxisTitle = $this->translator->trans($xAxisTitle, array(), 'chart');
        $yAxisTitle = $this->translator->trans($yAxisTitle, array(), 'chart');

        $ob = new Highchart();
        $ob->chart->renderTo($divId);
        $ob->title->text($title);

        $ob->xAxis->title(array('text'  => $xAxisTitle));
        $ob->xAxis->allowDecimals(false);
        $ob->xAxis->plotLines($this->generatePlotLines($series));

        $ob->yAxis->title(array('text' => $yAxisTitle));
        $ob->yAxis->allowDecimals(false);

        $ob->tooltip->useHTML('true');

        $formatter = new Expr('function () {
                 return "<b>' . $xAxisTitle .': </b>" + this.x + "<br>" +
                        "<b>' . $yAxisTitle .': </b>" + this.y + "<br>" +
                        "<b>' . $this->translator->trans('note', array(), 'chart') .': </b>" + this.point.note;
             }');

        $ob->tooltip->formatter($formatter);

        $ob->lang->downloadJPEG($this->translator->trans('download_jpeg_image', array(), 'chart'));
        $ob->lang->downloadPDF($this->translator->trans('download_pdf_document', array(), 'chart'));
        $ob->lang->downloadPNG($this->translator->trans('download_png_image', array(), 'chart'));
        $ob->lang->downloadSVG($this->translator->trans('download_svg_vector_image', array(), 'chart'));
        $ob->lang->printChart($this->translator->trans('print_chart', array(), 'chart'));

        $ob->series($this->translateSeriesName($series));

        return $ob;
    }

    private function getYAxisTitle($observationType) {

        return ($observationType == 'duration') ? 'number_of_intervals' : 'number_of_frequencies';
    }

    private function translateSeriesName(array $series) {
        foreach($series as &$dataSet) {
            if(!array_key_exists('name', $dataSet)) {
                throw new \Exception('Missing required key "Name"');
            }

            $dataSet['name'] = $this->translator->trans($dataSet['name'], array(), 'chart');
        }

        return $series;
    }
}