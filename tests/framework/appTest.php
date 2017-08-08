<?php

class AppTest extends \PHPUnit\Framework\TestCase {

    public function testObjectsRequestAndResponse() {
        $request = new \GuzzleHttp\Psr7\ServerRequest('get', '/');
        $this->assertInstanceOf(\Psr\Http\Message\ServerRequestInterface::class, $request);

        $response = new \GuzzleHttp\Psr7\Response();
        $this->assertInstanceOf(\Psr\Http\Message\ResponseInterface::class, $response);
    }

    public function testDeclareApplication() {
        $request = new \GuzzleHttp\Psr7\ServerRequest('get', '/');
        $response = new \GuzzleHttp\Psr7\Response();

        $app = new \BDSCore\Application\App(
            [
                'globalConfig' => \BDSCore\Config\Config::getAllConfig(),
                'securityConfig' => \BDSCore\Config\Config::getAllSecurityConfig()
            ],
            [
                'debugClass' => new \BDSCore\Debug\Debugger(),
                'securityClass' => new \BDSCore\Security\Security(),
                'routerClass' => new BDSCore\Router\Router($request, $response)
            ],
            $response
        );
        $this->assertInstanceOf(\BDSCore\Application\App::class, $app);
    }

    public function testFilePermissions() {
        $permsCode = substr(sprintf('%o', fileperms('./cache/')), -4);
        $this->assertTrue($permsCode == '0777');
    }

    public function testPhpVersion() {
        $phpMajorVersion = PHP_MAJOR_VERSION;
        $this->assertTrue($phpMajorVersion >= 7);
    }

}