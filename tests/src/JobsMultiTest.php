<?php namespace JobApis\Jobs\Client\Tests;

use JobApis\Jobs\Client\Collection;
use JobApis\Jobs\Client\Providers\AbstractProvider;
use Mockery as m;
use JobApis\Jobs\Client\JobsMulti;

class JobsMultiTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->providers = [
            'Careerbuilder' => [
                'DeveloperKey' => uniqid(),
            ],
            'Careercast' => [],
            'Dice' => [],
            'Github' => [],
            'Govt' => [],
            'Ieee' => [],
            'Indeed' => [
                'publisher' => uniqid(),
            ],
            'Jobinventory' => [],
            'Juju' => [
                'partnerid' => uniqid(),
            ],
            'Usajobs' => [
                'AuthorizationKey' => uniqid(),
            ],
            'Ziprecruiter' => [
                'api_key' => uniqid(),
            ],
        ];
        $this->client = new JobsMulti($this->providers);
    }

    public function testItCanInstantiateQueryObjectsWithAllProviders()
    {
        $queries = $this->getProtectedProperty($this->client, 'queries');

        foreach ($this->providers as $key => $provider) {
            $this->assertTrue(isset($queries[$key]));
            $this->assertEquals(
                'JobApis\\Jobs\\Client\\Queries\\'.$key.'Query',
                get_class($queries[$key])
            );
        }
    }

    public function testItCanInstantiateQueryObjectsWithoutAllProviders()
    {
        $providers = [
            'Dice' => [],
            'Govt' => [],
            'Github' => [],
        ];
        $client = new JobsMulti($providers);
        $queries = $this->getProtectedProperty($client, 'queries');

        foreach ($providers as $key => $provider) {
            $this->assertTrue(isset($queries[$key]));
            $this->assertEquals(
                'JobApis\\Jobs\\Client\\Queries\\'.$key.'Query',
                get_class($queries[$key])
            );
        }
        $this->assertFalse(isset($queries['careerbuilderQuery']));
        $this->assertFalse(isset($queries['indeedQuery']));
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testItThrowsErrorOnInvalidMethodCall()
    {
        $method = uniqid();
        $this->client->$method();
    }

    public function testItCanSetKeywordOnAllProviders()
    {
        $keyword = uniqid();
        $result = $this->client->setKeyword($keyword);

        $this->assertEquals(get_class($this->client), get_class($result));

        $queries = $this->getProtectedProperty($this->client, 'queries');

        foreach ($this->providers as $key => $provider) {
            switch ($key) {
                case 'Careerbuilder':
                    $this->assertEquals($keyword, $queries[$key]->get('Keywords'));
                break;
                case 'Careercast':
                    $this->assertEquals($keyword, $queries[$key]->get('keyword'));
                    break;
                case 'Careerjet':
                    $this->assertEquals($keyword, $queries[$key]->get('keywords'));
                    break;
                case 'Dice':
                    $this->assertEquals($keyword, $queries[$key]->get('text'));
                break;
                case 'Github':
                    $this->assertEquals($keyword, $queries[$key]->get('search'));
                    break;
                case 'Govt':
                    $this->assertEquals($keyword, $queries[$key]->get('query'));
                break;
                case 'Ieee':
                    $this->assertEquals($keyword, $queries[$key]->get('keyword'));
                    break;
                case 'Indeed':
                    $this->assertEquals($keyword, $queries[$key]->get('q'));
                    break;
                case 'Jobinventory':
                    $this->assertEquals($keyword, $queries[$key]->get('q'));
                    break;
                case 'Juju':
                    $this->assertEquals($keyword, $queries[$key]->get('k'));
                    break;
                case 'Usajobs':
                    $this->assertEquals($keyword, $queries[$key]->get('Keyword'));
                    break;
                case 'Ziprecruiter':
                    $this->assertEquals($keyword, $queries[$key]->get('search'));
                    break;
                default:
                    throw new \Exception("Provider {$key} not found in test.");
            }
        }
    }

    public function testItCanSetLocationOnAllProviders()
    {
        $city = uniqid();
        $state = 'te';
        $location = $city.', '.$state;
        $result = $this->client->setLocation($location);

        $this->assertEquals(get_class($this->client), get_class($result));

        $queries = $this->getProtectedProperty($this->client, 'queries');

        foreach ($this->providers as $key => $provider) {
            switch ($key) {
                case 'Careerbuilder':
                    $this->assertEquals($location, $queries[$key]->get('Location'));
                    break;
                case 'Careercast':
                    $this->assertEquals($location, $queries[$key]->get('location'));
                    break;
                case 'Careerjet':
                    $this->assertEquals($location, $queries[$key]->get('location'));
                    break;
                case 'Dice':
                    $this->assertEquals($city, $queries[$key]->get('city'));
                    $this->assertEquals($state, $queries[$key]->get('state'));
                    break;
                case 'Github':
                    $this->assertEquals($location, $queries[$key]->get('location'));
                    break;
                case 'Govt':
                    $this->assertNotEquals(false, strpos($queries[$key]->get('query'), 'in '.$location));
                    break;
                case 'Ieee':
                    $this->assertEquals($location, $queries[$key]->get('location'));
                    break;
                case 'Indeed':
                    $this->assertEquals($location, $queries[$key]->get('l'));
                    break;
                case 'Jobinventory':
                    $this->assertEquals($location, $queries[$key]->get('l'));
                    break;
                case 'Juju':
                    $this->assertEquals($location, $queries[$key]->get('l'));
                    break;
                case 'Usajobs':
                    $this->assertEquals($location, $queries[$key]->get('LocationName'));
                    break;
                case 'Ziprecruiter':
                    $this->assertEquals($location, $queries[$key]->get('location'));
                    break;
                default:
                    throw new \Exception("Provider {$key} not found in test.");
            }
        }
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testItCannotSetLocationOnProvidersWhenInvalid()
    {
        $location = uniqid().' '.uniqid();
        $this->client->setLocation($location);
    }

    public function testItCanSetPageOnAllProviders()
    {
        $page = rand(1, 20);
        $perPage = rand(1, 20);
        $startFrom = ($page * $perPage) - $perPage;
        $result = $this->client->setPage($page, $perPage);

        $this->assertEquals(get_class($this->client), get_class($result));

        $queries = $this->getProtectedProperty($this->client, 'queries');

        foreach ($this->providers as $key => $provider) {
            switch ($key) {
                case 'Careerbuilder':
                    $this->assertEquals($page, $queries[$key]->get('PageNumber'));
                    $this->assertEquals($perPage, $queries[$key]->get('PerPage'));
                    break;
                case 'Careercast':
                    $this->assertEquals($page, $queries[$key]->get('page'));
                    $this->assertEquals($perPage, $queries[$key]->get('rows'));
                    break;
                case 'Careerjet':
                    $this->assertEquals($page, $queries[$key]->get('page'));
                    $this->assertEquals($perPage, $queries[$key]->get('pagesize'));
                    break;
                case 'Dice':
                    $this->assertEquals($page, $queries[$key]->get('page'));
                    $this->assertEquals($perPage, $queries[$key]->get('pgcnt'));
                    break;
                case 'Github':
                    $this->assertEquals($page-1, $queries[$key]->get('page'));
                    break;
                case 'Govt':
                    $this->assertEquals($perPage, $queries[$key]->get('size'));
                    $this->assertEquals($startFrom, $queries[$key]->get('from'));
                    break;
                case 'Ieee':
                    $this->assertEquals($page, $queries[$key]->get('page'));
                    $this->assertEquals($perPage, $queries[$key]->get('rows'));
                    break;
                case 'Indeed':
                    $this->assertEquals($perPage, $queries[$key]->get('limit'));
                    $this->assertEquals($startFrom, $queries[$key]->get('start'));
                    break;
                case 'Jobinventory':
                    $this->assertEquals($page, $queries[$key]->get('p'));
                    $this->assertEquals($perPage, $queries[$key]->get('limit'));
                    break;
                case 'Juju':
                    $this->assertEquals($perPage, $queries[$key]->get('jpp'));
                    $this->assertEquals($page, $queries[$key]->get('page'));
                    break;
                case 'Usajobs':
                    $this->assertEquals($page, $queries[$key]->get('Page'));
                    $this->assertEquals($perPage, $queries[$key]->get('ResultsPerPage'));
                    break;
                case 'Ziprecruiter':
                    $this->assertEquals($page, $queries[$key]->get('page'));
                    $this->assertEquals($perPage, $queries[$key]->get('jobs_per_page'));
                    break;
                default:
                    throw new \Exception("Provider {$key} not found in test.");
            }
        }
    }

    public function testItCannotGetJobsByProviderWhenExceptionThrown()
    {
        $result = $this->client->getJobsByProvider(uniqid());

        $this->assertEquals(Collection::class, get_class($result));
        $this->assertNotNull($result->getErrors());
    }

    public function testItCanCreateProvider()
    {
        $provider = $this->getRandomProvider();
        $providerName = 'JobApis\\Jobs\\Client\\Providers\\' . $provider . 'Provider';
        $queryName = 'JobApis\\Jobs\\Client\\Queries\\'. $provider. 'Query';
        $result = JobsMulti::createProvider($providerName, new $queryName([]));

        $this->assertEquals($providerName, get_class($result));
    }

    public function testItCanGetResultsFromSingleApi()
    {
        if (!getenv('REAL_CALL')) {
            $this->markTestSkipped('REAL_CALL not set. Real API calls will not be made.');
        }

        $keyword = 'engineering';
        $providers = [
            'Dice' => [],
        ];
        $client = new JobsMulti($providers);

        $client->setKeyword($keyword);

        $results = $client->getDiceJobs();

        $this->assertInstanceOf('JobApis\Jobs\Client\Collection', $results);
        foreach($results as $job) {
            $this->assertEquals($keyword, $job->query);
        }
    }

    public function testItCanGetAllResultsFromApis()
    {
        if (!getenv('REAL_CALL')) {
            $this->markTestSkipped('REAL_CALL not set. Real API calls will not be made.');
        }

        $providers = [
            'Dice' => [],
            'Govt' => [],
            'Github' => [],
            'Jobinventory' => [],
        ];
        $client = new JobsMulti($providers);
        $keyword = 'engineering';

        $client->setKeyword($keyword)
            ->setLocation('Chicago, IL')
            ->setPage(1, 10);

        $jobs = $client->getAllJobs();

        foreach ($jobs as $provider => $results) {
            $this->assertInstanceOf('JobApis\Jobs\Client\Collection', $results);
            foreach($results as $job) {
                $this->assertEquals($keyword, $job->query);
            }
        }
    }

    private function getProtectedProperty($object, $property = null)
    {
        $class = new \ReflectionClass(get_class($object));
        $property = $class->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    private function getRandomProvider()
    {
        return array_rand($this->providers);
    }
}
