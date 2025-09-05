<?php
declare(strict_types=1);

namespace Test\Ecotone\JMSConverter\Fixture\EnumHeaderConversion;

use Ecotone\Messaging\Attribute\Asynchronous;
use Ecotone\Messaging\Attribute\Parameter\Header;
use Ecotone\Messaging\Attribute\Parameter\Headers;
use Ecotone\Modelling\Attribute\EventHandler;

/**
 * licence Apache-2.0
 */
class Playground
{
    public array $headers = [];
    public $singleHeaderWithTypehint;
    public $singleHeaderWithoutTypehint;
    public $numericEnum;

    #[Asynchronous(channelName: 'async')]
    #[EventHandler(listenTo: 'message', endpointId: 'message.async')]
    public function eventHandler(
        #[Headers] array                     $headers,
        #[Header('withTypeHint')] StringEnum $singleHeaderWithTypehint,
        #[Header('withoutTypeHint')]         $singleHeaderWithoutTypehint,
        #[Header('numeric')] NumericEnum     $numericEnum,
    ): void {
        $this->headers = $headers;
        $this->singleHeaderWithTypehint = $singleHeaderWithTypehint;
        $this->singleHeaderWithoutTypehint = $singleHeaderWithoutTypehint;
        $this->numericEnum = $numericEnum;
    }
}
