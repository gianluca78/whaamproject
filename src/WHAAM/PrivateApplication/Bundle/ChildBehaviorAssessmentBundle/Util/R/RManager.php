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
namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Util\R;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Util\BehavioralDataChart\BehavioralDataGenerator;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessment;

class RManager
{
    CONST TAU_ANALYSIS_MINIMUM_OBSERVATION_NUMBER = 4;

    private $datafilePath;
    private $behavioralDataGenerator;
    private $tauScriptPath;
    private $translator;

    public function __construct($datafilePath, BehavioralDataGenerator $behavioralDataGenerator, $tauScriptPath, Translator $translator)
    {
        $this->behavioralDataGenerator = $behavioralDataGenerator;
        $this->datafilePath = $datafilePath;
        $this->tauScriptPath = $tauScriptPath;
        $this->translator = $translator;
    }

    public function calculateTau(ChildBehaviorAssessment $assessment)
    {
        if($assessment->getBaselines()->count() == 0 ||
            !$assessment->hasAtLeastAnIntervention()
        ) {
            return null;
        }

        $baseline = $assessment->getBaselines()->first();
        $intervention = $baseline->getIntervention();
        $baselineObservationSessions = $baseline->getObservationSessions();
        $interventionObservationSessions = $intervention->getObservationSessions();

        if(count($baselineObservationSessions) < self::TAU_ANALYSIS_MINIMUM_OBSERVATION_NUMBER ||
            !$intervention ||
            count($interventionObservationSessions) < self::TAU_ANALYSIS_MINIMUM_OBSERVATION_NUMBER
        ) {
            return null;
        }

        $filename = 'datafile-' . $assessment->getId() . '.R';

        $content = $this->generateTauDatafileContentFromPoints($assessment);

        $this->writeTauDataFile($filename, $content);

        $result = exec('R --slave -f ' . $this->tauScriptPath. ' --args ' . $filename);

        return $result;
    }

    public function getAVsBTauSentence(ChildBehaviorAssessment $assessment)
    {
        $data = json_decode($this->calculateTau($assessment));

        $tau = $data->PartitionsOfMatrix[10]->AvsB;

        return $this->getTauResultMessage($tau);
    }

    public function getAVsBPlusTrendBTauSentence(ChildBehaviorAssessment $assessment)
    {
        $data = json_decode($this->calculateTau($assessment));
        $tau = $data->TAU_U_Analysis[10]->AvsBTrendB;

        return $this->getTauResultMessage($tau);
    }

    public function getAVsBPlusTrendBMinusTrendATauSentence(ChildBehaviorAssessment $assessment)
    {
        $data = json_decode($this->calculateTau($assessment));
        $tau = $data->TAU_U_Analysis[10]->AvsBTrendBTrendA;

        return $this->getTauResultMessage($tau);
    }

    public function getTauEffectSizeSentence($tau)
    {
        $effectSize = sprintf(
            $this->translator->trans('effect_size_sentence', array(), 'r_analysis'),
            round($tau, 3)
        );

        return $effectSize;
    }

    public function writeTauDataFile($filename, $content)
    {
        try {
            $datafile = fopen($this->datafilePath . '/' . $filename, "w");
            fwrite($datafile, $content);
            fclose($datafile);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    private function generateTauDatafileContentFromPoints(ChildBehaviorAssessment $assessment)
    {
        $observationType = $assessment->getBaselines()->first()->getObservationType();
        $startingInterventionSessionNumber = 1;

        $baseline = $assessment->getBaselines()->first();

        $baselinePoints = ($observationType == 'duration') ?
            $this->behavioralDataGenerator->generateDurationDataSet(
                $baseline->getObservationSessions(),
                $startingInterventionSessionNumber
            )
            : $this->behavioralDataGenerator->generateFrequencyDataSet(
                $baseline->getObservationSessions(),
                $startingInterventionSessionNumber
            );

        $startingInterventionSessionNumber += count($baselinePoints);

        $intervention = $baseline->getIntervention();

        $interventionPoints = ($observationType == 'duration') ?
            $this->behavioralDataGenerator->generateDurationDataSet(
                $intervention->getObservationSessions(),
                $startingInterventionSessionNumber
            )
            : $this->behavioralDataGenerator->generateFrequencyDataSet(
                $intervention->getObservationSessions(),
                $startingInterventionSessionNumber
            );

        $baselineDataSet = array();
        $interventionDataSet = array();

        foreach($baselinePoints as $points) {
            $baselineDataSet[] = $points['y'];
        }

        foreach($interventionPoints as $points) {
            $interventionDataSet[] = $points['y'];
        }

        $baselinePhaseName = array_fill(1, count($baselineDataSet), '"A"');
        $interventionPhaseName = array_fill(count($baselineDataSet), count($interventionDataSet), '"B"');

        $datafileContent = 'data = c(' . implode(',', $baselineDataSet) . ',' . implode(',', $interventionDataSet) . ')' . PHP_EOL;
        $datafileContent .= 'fase = c(' . implode(',', $baselinePhaseName) . ',' . implode(',', $interventionPhaseName) . ')';

        return $datafileContent;
    }

    private function getTauResultMessage($tau)
    {
        $sign = ($tau > 0) ? $this->translator->trans('increase', array(), 'r_analysis')
            : $this->translator->trans('decrease', array(), 'r_analysis');

        if ($this->hasTauNotEffect($tau)) {
            return $this->translator->trans('treatment_no_effect', array(), 'r_analysis');;
        }

        if ($this->hasTauSmallEffect($tau)) {
            $small = $this->translator->trans('small', array(), 'r_analysis');
            $message = sprintf($this->translator->trans('treatment_effect', array(), 'r_analysis'), $small, $sign);

            return $message;
        }

        if ($this->hasTauMediumEffect($tau)) {
            $small = $this->translator->trans('medium', array(), 'r_analysis');
            $message = sprintf($this->translator->trans('treatment_effect', array(), 'r_analysis'), $small, $sign);

            return $message;
        }

        if ($this->hasTauLargeEffect($tau)) {
            $small = $this->translator->trans('large', array(), 'r_analysis');
            $message = sprintf($this->translator->trans('treatment_effect', array(), 'r_analysis'), $small, $sign);

            return $message;
        }
    }

    private function hasTauNotEffect($tau)
    {
        return ($tau <= 0.1 && $tau >= -0.1) ? true : false;
    }

    private function hasTauSmallEffect($tau)
    {
        $tau = abs($tau);

        return ($tau >= 0.1 && $tau <= 0.3) ? true : false;
    }

    private function hasTauMediumEffect($tau)
    {
        $tau = abs($tau);

        return ($tau >= 0.3 && $tau <= 0.5) ? true : false;
    }

    private function hasTauLargeEffect($tau)
    {
        $tau = abs($tau);

        return ($tau >= 0.5) ? true : false;
    }


}