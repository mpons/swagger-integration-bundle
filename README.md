## Swagger integration Bundle

Automatically generate [Swagger](http://swagger.io) documentation from integration tests in Symfony:

```

    /**
     * @test
     *
     * @SwaggerRequest(description="test description", summary="test summary")
     * @SwaggerResponse(description="test response 200", model="Acme\Bundle\AppBundle\Model\TestModelDto")
     * @SwaggerHeaders(include="['x-app-build-version', 'x-app-platform', 'authorization']")
     */
    public function get_mobile_campaigns_should_result_in_correct_response()
    {
        $headers = [
            'HTTP_Content-Type' => 'application/json',
            'HTTP_Authorization' => '01234567-aaaa-4ccc-01234-abcdef123456',
        ];

        $this->client->request('GET', '/test-endpoint', [], [], $headers);
        $this->assertEquals(HTTP::OK, $this->client->getResponse()->getStatusCode());
    }
```

### Install

* composer install mpons/swagger-integration-bundle

### Configure

* Register the bundle in the Kernel (AppKernel.php):

```
public function registerBundles()
{
    $bundles = [
        ...
    ];

    if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
        ...

        if ('test' === $this->getEnvironment()) {
            $bundles[] = new Mpons\SwaggerIntegrationBundle\MponsSwaggerIntegrationBundle();
        }
    }

    return $bundles;
}
```

* Register the bundle as a listener for your tests (in phpunit.xml.dist):

```

    <listeners>
        <listener class="Mpons\SwaggerIntegrationBundle\EventListener\SwaggerIntegrationTestListener" file="vendor/mpons/swagger-integration-bundle/src/Mpons/SwaggerIntegrationBundle/EventListener/SwaggerIntegrationTestListener.php" />
    </listeners>

```

* Configure the bundle in symfony (config_test.yml):

_As the config is meant to be in config_test, no parameter is required there. Though, failing to configure json_path will throw an error_

```
mpons_swagger_integration:
    info: 'test info'
    name: 'test name'
    version: 'test version'
    json_path: '%kernel.project_dir%/doc/swagger/resources/swagger.json'
    servers:
        - url: http://api.staging.url
          description: 'Staging URL'
        - url: http://api.production.url
          description: 'Production URL'

```

### Usage
