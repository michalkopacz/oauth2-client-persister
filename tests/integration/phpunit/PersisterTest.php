<?php

namespace MostSignificantBit\OAuth2\Client\Persister\Tests\Integration;

use MostSignificantBit\OAuth2\Client\Client as OAuth2Client;
use MostSignificantBit\OAuth2\Client\Config\Config;
use MostSignificantBit\OAuth2\Client\AccessToken\SuccessfulResponse as AccessTokenSuccessfulResponse;
use MostSignificantBit\OAuth2\Client\Grant\AuthorizationCode\AccessTokenRequest as AuthorizationCodeGrantAccessTokenRequest;
use MostSignificantBit\OAuth2\Client\Grant\AuthorizationCode\AuthorizationCodeGrant;
use MostSignificantBit\OAuth2\Client\Grant\ClientCredentials\AccessTokenRequest as ClientCredentialsGrantAccessTokenRequest;
use MostSignificantBit\OAuth2\Client\Grant\ClientCredentials\ClientCredentialsGrant;
use MostSignificantBit\OAuth2\Client\Grant\RefreshToken\AccessTokenRequest;
use MostSignificantBit\OAuth2\Client\Grant\RefreshToken\RefreshTokenGrant;
use MostSignificantBit\OAuth2\Client\Grant\ResourceOwnerPasswordCredentials\AccessTokenRequest as ResourceOwnerPasswordCredentialsAccessTokenRequest;
use MostSignificantBit\OAuth2\Client\Grant\ResourceOwnerPasswordCredentials\ResourceOwnerPasswordCredentialsGrant;
use MostSignificantBit\OAuth2\Client\Parameter\AccessToken;
use MostSignificantBit\OAuth2\Client\Parameter\Code;
use MostSignificantBit\OAuth2\Client\Parameter\ExpiresIn;
use MostSignificantBit\OAuth2\Client\Parameter\Password;
use MostSignificantBit\OAuth2\Client\Parameter\RefreshToken;
use MostSignificantBit\OAuth2\Client\Parameter\TokenType;
use MostSignificantBit\OAuth2\Client\Parameter\Username;
use MostSignificantBit\OAuth2\Client\Persister\Persister;
use MostSignificantBit\OAuth2\Client\Persister\Storage\MemoryStorage;
use MostSignificantBit\OAuth2\Client\Persister\User\Identity;
use MostSignificantBit\OAuth2\Client\Persister\User\SimpleIdentityProvider;
use Symfony\Component\Process\Process;

/**
 * @group integration
 */
class PersisterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Process
     */
    protected static $serverProcess;

    public static function setUpBeforeClass()
    {
        $testDir = dirname(dirname(__DIR__));

        $cmd = "php -S 127.0.0.1:8000 -t {$testDir}/server/web {$testDir}/server/web/index.php";

        self::$serverProcess = new Process($cmd);
        self::$serverProcess->start();
    }

    public function testObtainAccessTokenFromStorage()
    {
        $config = $this->getConfig();

        $oauth2Client = new OAuth2Client($config);

        $accessTokenExpectedResponse = new AccessTokenSuccessfulResponse(new AccessToken('2YotnFZFEjr1zCsicMWpAA'), TokenType::BEARER());
        $accessTokenExpectedResponse->setExpiresIn(new ExpiresIn(3600));
        $accessTokenExpectedResponse->setRefreshToken(new RefreshToken('tGzv3JOkF0XG5Qx2TlKWIA'));

        $accessTokenRequest = new AuthorizationCodeGrantAccessTokenRequest(new Code('SplxlOBeZQQYbYS6WxSbIA'));

        $grant = new AuthorizationCodeGrant($accessTokenRequest);

        $storage = new MemoryStorage();

        $persister = new Persister($oauth2Client, $storage);

        $identity = new Identity(1);

        $identityProvider = new SimpleIdentityProvider($identity);

        $accessToken = $persister->obtainAccessToken($identityProvider, $grant);

        $accessToken2 = $persister->getAccessToken($identity);

        $this->assertEquals($accessToken->getValue(), $accessToken2->getValue());
    }

    public function testRefreshAccessTokenAfterExpiration()
    {
        $config = $this->getConfig();

        $oauth2Client = new OAuth2Client($config);

        $accessTokenExpectedResponse = new AccessTokenSuccessfulResponse(new AccessToken('2YotnFZFEjr1zCsicMWpAA'), TokenType::BEARER());
        $accessTokenExpectedResponse->setExpiresIn(new ExpiresIn(3600));
        $accessTokenExpectedResponse->setRefreshToken(new RefreshToken('tGzv3JOkF0XG5Qx2TlKWIA'));

        $accessTokenRequest = new AuthorizationCodeGrantAccessTokenRequest(new Code('SplxlOBeZQQYbYS6WxSbIA'));

        $grant = new AuthorizationCodeGrant($accessTokenRequest);

        $storage = new MemoryStorage();

        $persister = new Persister($oauth2Client, $storage);

        $identity = new Identity(1);

        $identityProvider = new SimpleIdentityProvider($identity);

        $accessToken = $persister->obtainAccessToken($identityProvider, $grant);

        $this->waitForExpiration();

        $accessToken2 = $persister->getAccessToken($identity);

        $this->assertNotEquals($accessToken->getValue(), $accessToken2->getValue());
    }

    public static function tearDownAfterClass()
    {
        self::$serverProcess->stop();
    }

    protected function getConfig()
    {
        return new  Config(array(
            'endpoint' => array(
                'token_endpoint_uri' => 'http://127.0.0.1:8000/oauth2/token',
            ),
            'client' => array(
                'credentials' => array(
                    'client_id' => 's6BhdRkqt3',
                    'client_secret' => '7Fjfp0ZBr1KtDRbnfVdmIw',
                ),
            ),
        ));
    }

    protected function waitForExpiration()
    {
        sleep(3);
    }
} 