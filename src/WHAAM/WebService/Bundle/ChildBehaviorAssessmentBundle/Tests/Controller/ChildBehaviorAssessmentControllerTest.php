<?php

namespace WHAAM\WebService\Bundle\ChildBehaviorAssessmentBundle\Tests\Controller;

use WHAAM\PrivateApplication\Common\Tests\CustomWebTestCase;

class ChildBehaviorAssessmentControllerTest extends CustomWebTestCase
{
    protected $client;
    protected $context;
    protected $entityManager;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->container = $kernel->getContainer();

        $this->entityManager = $this->container->get('doctrine.orm.entity_manager');
        $this->context       = $this->container->get('security.context');
    }

    public function __construct()
    {
        $this->client = static::createClient();
    }

    /**
     * Test observations with right parameters
     */
    public function testObservations()
    {
        $crawler = $this->client->request(
            'POST',
            'https://127.0.0.66/api/login?apikey=a3332be7d986075cbd715f3ffc183bc7',
            array(
                'username' => 'test',
                'password' => 'test',
            )
        );

        $user = $this->entityManager->getRepository('WHAAMPrivateApplicationUserBundle:User')->find(1);

        $crawler = $this->client->request(
            'GET',
            'https://127.0.0.66/api/child-behavior-assessment/observations/3/baseline/' . $user->getMobileAppSessionId() . '?apikey=a3332be7d986075cbd715f3ffc183bc7'
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 200);
    }

    /**
     * Test observations with invalid parameters
     */
    public function testInvalidObservations()
    {
        $crawler = $this->client->request(
            'POST',
            'https://127.0.0.66/api/login?apikey=a3332be7d986075cbd715f3ffc183bc7',
            array(
                'username' => 'test',
                'password' => 'test',
            )
        );

        $user = $this->entityManager->getRepository('WHAAMPrivateApplicationUserBundle:User')->find(1);

        $crawler = $this->client->request(
            'GET',
            'https://127.0.0.66/api/child-behavior-assessment/observations/3/invalid/' . $user->getMobileAppSessionId() . '?apikey=a3332be7d986075cbd715f3ffc183bc7'
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 400);
    }


    /**
     * Test observation with right parameters
     */
    public function testChildBehaviorAssessmentPostObservationData()
    {
        $user = $this->entityManager->getRepository('WHAAMPrivateApplicationUserBundle:User')->find(1);

        $baseline = $this->entityManager->createQuery(
                'SELECT b
                FROM WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline b'
                )
            ->setMaxResults(1)
            ->getSingleResult();

        $crawler = $this->client->request(
            'POST',
            'https://127.0.0.66/api/child-behavior-assessment/observations?apikey=a3332be7d986075cbd715f3ffc183bc7',
            array(
                'sessionId' => $user->getMobileAppSessionId(),
                'observationSessions' => array(
                    array(
                        'phaseId' => $baseline->getId(),
                        'phaseName' => 'baseline',
                        'note' => 'session test 1',
                        'sessionStartTimestamp' => time(),
                        'timestamps' => array(
                            time(),
                            time(),
                            time(),
                            time(),
                            time(),
                        )
                    ),
                    array(
                        'phaseId' => $baseline->getId(),
                        'phaseName' => 'baseline',
                        'note' => 'session test 2',
                        'sessionStartTimestamp' => time(),
                        'timestamps' => array(
                            time(),
                            time(),
                            time(),
                            time(),
                            time(),
                        )
                    ),
                    array(
                        'phaseId' => 2,
                        'phaseName' => 'baseline',
                        'note' => 'session test 2',
                        'sessionStartTimestamp' => time(),
                        'timestamps' => array(
                            time(),
                            time(),
                            time(),
                            time(),
                            time(),
                        )
                    ),
                )
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 200);
    }

    /**
     * Test observation with invalid parameters
     */
    public function testInvalidChildBehaviorAssessmentPostObservationData()
    {
        $user = $this->entityManager->getRepository('WHAAMPrivateApplicationUserBundle:User')->find(1);

        $baseline = $this->entityManager->createQuery(
            'SELECT b
            FROM WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline b'
        )
            ->setMaxResults(1)
            ->getSingleResult();

        $crawler = $this->client->request(
            'POST',
            'https://127.0.0.66/api/child-behavior-assessment/observations?apikey=a3332be7d986075cbd715f3ffc183bc7',
            array(
                'sessionId' => $user->getMobileAppSessionId(),
                'observationSessions' => array(
                    array(
                        'phaseId' => $baseline->getId(),
                        'phase' => 'baseline',
                        'note' => 'session test 1',
                        'sessionStartTimestamp' => time(),
                        'timestamps' => array(
                            time(),
                            time(),
                            time(),
                            time(),
                            time(),
                        )
                    ),
                    array(
                        'phaseId' => $baseline->getId(),
                        'phaseName' => 'baseline',
                        'note' => 'session test 2',
                        'sessionStartTimestamp' => time(),
                        'timestamps' => array(
                            time(),
                            time(),
                            time(),
                            time(),
                            time(),
                        )
                    ),
                )
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 400);
    }

    /**
     * Test observation with invalid baseline object
     */
    public function testInvalidBaselineChildBehaviorAssessmentPostObservationData()
    {
        $user = $this->entityManager->getRepository('WHAAMPrivateApplicationUserBundle:User')->find(1);

        $crawler = $this->client->request(
            'POST',
            'https://127.0.0.66/api/child-behavior-assessment/observations?apikey=a3332be7d986075cbd715f3ffc183bc7',
            array(
                'sessionId' => $user->getMobileAppSessionId(),
                'observationSessions' => array(
                    array(
                        'phaseId' => 'test',
                        'phaseName' => 'baseline',
                        'note' => 'session test 1',
                        'sessionStartTimestamp' => time(),
                        'timestamps' => array(
                            time(),
                            time(),
                            time(),
                            time(),
                            time(),
                        )
                    ),
                    array(
                        'phaseId' => 'test',
                        'phaseName' => 'baseline',
                        'note' => 'session test 2',
                        'sessionStartTimestamp' => time(),
                        'timestamps' => array(
                            time(),
                            time(),
                            time(),
                            time(),
                            time(),
                        )
                    ),
                )
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 400);
    }

    /**
     * Test observation with invalid timestamp
     */
    public function testInvalidTimestampChildBehaviorAssessmentPostObservationData()
    {
        $user = $this->entityManager->getRepository('WHAAMPrivateApplicationUserBundle:User')->find(1);

        $baseline = $this->entityManager->createQuery(
            'SELECT b
            FROM WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline b'
        )
            ->setMaxResults(1)
            ->getSingleResult();

        $crawler = $this->client->request(
            'POST',
            'https://127.0.0.66/api/child-behavior-assessment/observations?apikey=a3332be7d986075cbd715f3ffc183bc7',
            array(
                'sessionId' => $user->getMobileAppSessionId(),
                'observationSessions' => array(
                    array(
                        'phaseId' => $baseline->getId(),
                        'phaseName' => 'baseline',
                        'note' => 'session test 1',
                        'sessionStartTimestamp' => time(),
                        'timestamps' => array(
                            time(),
                            time(),
                            time(),
                            time(),
                            time(),
                        )
                    ),
                    array(
                        'phaseId' => $baseline->getId(),
                        'phaseName' => 'baseline',
                        'note' => 'session test 2',
                        'sessionStartTimestamp' => time(),
                        'timestamps' => array(
                            time(),
                            'invalid timestamp',
                            time(),
                            time(),
                            time(),
                        )
                    ),
                )
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 400);
    }

    /**
     * Test ABC with right parameters
     */
    public function testChildBehaviorAssessmentPostABCs()
    {
        $user = $this->entityManager->getRepository('WHAAMPrivateApplicationUserBundle:User')->find(1);

        $baseline = $this->entityManager->createQuery(
            'SELECT b
            FROM WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline b'
        )
            ->setMaxResults(1)
            ->getSingleResult();

        $crawler = $this->client->request(
            'POST',
            'https://127.0.0.66/api/child-behavior-assessment/abcs?apikey=a3332be7d986075cbd715f3ffc183bc7',
            array(
                'sessionId' => $user->getMobileAppSessionId(),
                'ABCs' => array(
                    array(
                        'baselineId' => $baseline->getId(),
                        'userId' => $user->getId(),
                        'abcDateTimestamp' => time(),
                        'antecedentWhere' => 'test',
                        'antecedentWhat' => 'test',
                        'antecedentWho' => 'test',
                        'antecedentTrigger' => 'test',
                        'consequenceChildReaction' => 'test',
                        'consequenceOthersReaction' => 'test',
                    ),
                    array(
                        'baselineId' => $baseline->getId(),
                        'userId' => $user->getId(),
                        'abcDateTimestamp' => time(),
                        'antecedentWhere' => 'test2',
                        'antecedentWhat' => 'test2',
                        'antecedentWho' => 'test2',
                        'antecedentTrigger' => 'test2',
                        'consequenceChildReaction' => 'test2',
                        'consequenceOthersReaction' => 'test2',
                    ),
                    array(
                        'baselineId' => 2,
                        'userId' => $user->getId(),
                        'abcDateTimestamp' => time(),
                        'antecedentWhere' => 'test2',
                        'antecedentWhat' => 'test2',
                        'antecedentWho' => 'test2',
                        'antecedentTrigger' => 'test2',
                        'consequenceChildReaction' => 'test2',
                        'consequenceOthersReaction' => 'test2',
                    )
                )
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 200);
    }

    /**
     * Test ABC with missing required parameters
     */
    public function testMissingParametersChildBehaviorAssessmentPostABCs()
    {
        $user = $this->entityManager->getRepository('WHAAMPrivateApplicationUserBundle:User')->find(1);

        $baseline = $this->entityManager->createQuery(
            'SELECT b
            FROM WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline b'
        )
            ->setMaxResults(1)
            ->getSingleResult();

        $crawler = $this->client->request(
            'POST',
            'https://127.0.0.66/api/child-behavior-assessment/abcs?apikey=a3332be7d986075cbd715f3ffc183bc7',
            array(
                'sessionId' => $user->getMobileAppSessionId(),
                'ABCs' => array(
                    array(
                        'baselineId' => $baseline->getId(),
                        'userId' => $user->getId(),
                        'abcDateTimestamp' => time(),
                        'antecedentWhat' => 'test',
                        'antecedentWho' => 'test',
                        'antecedentTrigger' => 'test',
                        'consequenceChildReaction' => 'test',
                        'consequenceOthersReaction' => 'test',
                    ),
                    array(
                        'baselineId' => $baseline->getId(),
                        'userId' => $user->getId(),
                        'abcDateTimestamp' => time(),
                        'antecedentWhere' => 'test2',
                        'antecedentWhat' => 'test2',
                        'antecedentWho' => 'test2',
                        'antecedentTrigger' => 'test2',
                        'consequenceChildReaction' => 'test2',
                        'consequenceOthersReaction' => 'test2',
                    )
                )
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 400);
    }

    /**
     * Test ABC with invalid date
     */
    public function testInvalidDateChildBehaviorAssessmentPostABCs()
    {
        $user = $this->entityManager->getRepository('WHAAMPrivateApplicationUserBundle:User')->find(1);

        $baseline = $this->entityManager->createQuery(
            'SELECT b
            FROM WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline b'
        )
            ->setMaxResults(1)
            ->getSingleResult();

        $crawler = $this->client->request(
            'POST',
            'https://127.0.0.66/api/child-behavior-assessment/abcs?apikey=a3332be7d986075cbd715f3ffc183bc7',
            array(
                'sessionId' => $user->getMobileAppSessionId(),
                'ABCs' => array(
                    array(
                        'baselineId' => $baseline->getId(),
                        'userId' => $user->getId(),
                        'abcDateTimestamp' => 'invalid date',
                        'antecedentWhere' => 'test',
                        'antecedentWhat' => 'test',
                        'antecedentWho' => 'test',
                        'antecedentTrigger' => 'test',
                        'consequenceChildReaction' => 'test',
                        'consequenceOthersReaction' => 'test',
                    ),
                    array(
                        'baselineId' => $baseline->getId(),
                        'userId' => $user->getId(),
                        'abcDateTimestamp' => time(),
                        'antecedentWhere' => 'test2',
                        'antecedentWhat' => 'test2',
                        'antecedentWho' => 'test2',
                        'antecedentTrigger' => 'test2',
                        'consequenceChildReaction' => 'test2',
                        'consequenceOthersReaction' => 'test2',
                    )
                )
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 400);
    }

    /**
     * Test ABC with wrong baseline
     */
    public function testInvalidBaselineChildBehaviorAssessmentPostABCs()
    {
        $user = $this->entityManager->getRepository('WHAAMPrivateApplicationUserBundle:User')->find(1);

        $baseline = $this->entityManager->createQuery(
            'SELECT b
            FROM WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline b'
        )
            ->setMaxResults(1)
            ->getSingleResult();

        $crawler = $this->client->request(
            'POST',
            'https://127.0.0.66/api/child-behavior-assessment/abcs?apikey=a3332be7d986075cbd715f3ffc183bc7',
            array(
                'sessionId' => $user->getMobileAppSessionId(),
                'ABCs' => array(
                    array(
                        'baselineId' => 'wrong baseline',
                        'userId' => $user->getId(),
                        'abcDateTimestamp' => time(),
                        'antecedentWhere' => 'test',
                        'antecedentWhat' => 'test',
                        'antecedentWho' => 'test',
                        'antecedentTrigger' => 'test',
                        'consequenceChildReaction' => 'test',
                        'consequenceOthersReaction' => 'test',
                    ),
                    array(
                        'baselineId' => $baseline->getId(),
                        'userId' => $user->getId(),
                        'abcDateTimestamp' => time(),
                        'antecedentWhere' => 'test2',
                        'antecedentWhat' => 'test2',
                        'antecedentWho' => 'test2',
                        'antecedentTrigger' => 'test2',
                        'consequenceChildReaction' => 'test2',
                        'consequenceOthersReaction' => 'test2',
                    )
                )
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, 400);
    }

}