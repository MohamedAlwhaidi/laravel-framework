<?php

namespace Illuminate\Tests\Support;

use Carbon\Carbon as BaseCarbon;
use Carbon\CarbonImmutable as BaseCarbonImmutable;
use DateTimeInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\CarbonImmutable;
use PHPUnit\Framework\TestCase;

class SupportCarbonImmutableTest extends TestCase
{
    /**
     * @var \Illuminate\Support\CarbonImmutable
     */
    protected $now;

    protected function setUp(): void
    {
        parent::setUp();

        CarbonImmutable::setTestNow($this->now = CarbonImmutable::create(2017, 6, 27, 13, 14, 15, 'UTC'));
    }

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow(null);
        parent::tearDown();
    }

    public function testInstance()
    {
        $this->assertInstanceOf(CarbonImmutable::class, $this->now);
        $this->assertInstanceOf(DateTimeInterface::class, $this->now);
        $this->assertInstanceOf(BaseCarbonImmutable::class, $this->now);
    }

    public function testCarbonCanSerializeToJson()
    {
        $this->assertSame('2017-06-27T13:14:15.000000Z', $this->now->jsonSerialize());
    }

    public function testSetStateReturnsCorrectType()
    {
        $carbon = CarbonImmutable::__set_state([
            'date' => '2017-06-27 13:14:15.000000',
            'timezone_type' => 3,
            'timezone' => 'UTC',
        ]);

        $this->assertInstanceOf(CarbonImmutable::class, $carbon);
    }

    public function testSetTestNowWillPersistBetweenImmutableAndMutableInstance()
    {
        CarbonImmutable::setTestNow(new Carbon('2017-06-27 13:14:15.000000'));

        $this->assertSame('2017-06-27 13:14:15', Carbon::now()->toDateTimeString());
        $this->assertSame('2017-06-27 13:14:15', CarbonImmutable::now()->toDateTimeString());
        $this->assertSame('2017-06-27 13:14:15', BaseCarbon::now()->toDateTimeString());
        $this->assertSame('2017-06-27 13:14:15', BaseCarbonImmutable::now()->toDateTimeString());
    }

    public function testImmutableCarbonIsConditionable()
    {
        $this->assertTrue(CarbonImmutable::now()->when(null, fn (CarbonImmutable $carbon) => $carbon->addDays(1))->isToday());
        $this->assertTrue(CarbonImmutable::now()->when(true, fn (CarbonImmutable $carbon) => $carbon->addDays(1))->isTomorrow());
    }
}
