<?php

namespace Kirby\Kql;

use Kirby\Cms\App;
use Kirby\Cms\Blueprint;
use Kirby\Cms\Content;
use Kirby\Cms\Field;
use Kirby\Cms\File;
use Kirby\Cms\FileBlueprint;
use Kirby\Cms\FileVersion;
use Kirby\Cms\Page;
use Kirby\Cms\PageBlueprint;
use Kirby\Cms\Role;
use Kirby\Cms\Site;
use Kirby\Cms\SiteBlueprint;
use Kirby\Cms\User;
use Kirby\Cms\UserBlueprint;
use PHPUnit\Framework\TestCase;

class AppExtended extends App
{
}
class FileExtended extends File
{
}
class PageExtended extends Page
{
}
class RoleExtended extends User
{
}
class SiteExtended extends Site
{
}
class UserExtended extends User
{
}

class InterceptorTest extends TestCase
{
    public function objectProvider()
    {
        return [
            [
                new App(),
                'Kirby\\Kql\\Interceptors\\Cms\\App'
            ],
            [
                new AppExtended(),
                'Kirby\\Kql\\Interceptors\\Cms\\App'
            ],
            [
                new Blueprint([
                    'model' => new Page([
                        'slug' => 'test'
                    ]),
                    'name'  => 'test',
                ]),
                'Kirby\\Kql\\Interceptors\\Cms\\Blueprint'
            ],
            [
                new Content(),
                'Kirby\\Kql\\Interceptors\\Cms\\Content'
            ],
            [
                new Field(null, 'key', 'value'),
                'Kirby\\Kql\\Interceptors\\Cms\\Field'
            ],
            [
                new File(['filename' => 'test.jpg']),
                'Kirby\\Kql\\Interceptors\\Cms\\File'
            ],
            [
                new FileBlueprint([
                    'model' => new File([
                        'filename' => 'test.jpg'
                    ]),
                    'name' => 'test',
                ]),
                'Kirby\\Kql\\Interceptors\\Cms\\Blueprint'
            ],
            [
                new FileExtended(['filename' => 'test.jpg']),
                'Kirby\\Kql\\Interceptors\\Cms\\File'
            ],
            [
                new FileVersion([
                    'original' => new File([
                        'filename' => 'test.jpg',
                    ]),
                    'url' => '/test.jpg'
                ]),
                'Kirby\\Kql\\Interceptors\\Cms\\FileVersion'
            ],
            [
                new Page(['slug' => 'test']),
                'Kirby\\Kql\\Interceptors\\Cms\\Page'
            ],
            [
                new PageBlueprint([
                    'model' => new Page([
                        'slug' => 'test'
                    ]),
                    'name'  => 'test',
                ]),
                'Kirby\\Kql\\Interceptors\\Cms\\Blueprint'
            ],
            [
                new PageExtended(['slug' => 'test']),
                'Kirby\\Kql\\Interceptors\\Cms\\Page'
            ],
            [
                new Role(['name' => 'admin']),
                'Kirby\\Kql\\Interceptors\\Cms\\Role'
            ],
            [
                new Site(),
                'Kirby\\Kql\\Interceptors\\Cms\\Site'
            ],
            [
                new SiteBlueprint([
                    'model' => new Site(),
                    'name'  => 'test',
                ]),
                'Kirby\\Kql\\Interceptors\\Cms\\Blueprint'
            ],
            [
                new SiteExtended(),
                'Kirby\\Kql\\Interceptors\\Cms\\Site'
            ],
            [
                new User(['email' => 'test@getkirby.com']),
                'Kirby\\Kql\\Interceptors\\Cms\\User'
            ],
            [
                new UserBlueprint([
                    'model' => new User(['email' => 'test@getkirby.com']),
                    'name'  => 'test',
                ]),
                'Kirby\\Kql\\Interceptors\\Cms\\Blueprint'
            ],
            [
                new UserExtended(['email' => 'test@getkirby.com']),
                'Kirby\\Kql\\Interceptors\\Cms\\User'
            ]
        ];
    }

    /**
     * @dataProvider objectProvider
     */
    public function testReplace($object, $inspector)
    {
        $result = Interceptor::replace($object);
        $this->assertInstanceOf($inspector, $result);
    }

    public function testReplaceNonObject()
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Unsupported value: string');

        $result = Interceptor::replace('hello');
    }

    public function testReplaceUnknownObject()
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Unsupported object: stdClass');

        $object = new \stdClass();
        $result = Interceptor::replace($object);
    }
}
