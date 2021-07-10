<?php

/**
 * PHPMailer - PHP email transport unit tests.
 * PHP version 5.5.
 *
 * @author    Marcus Bointon <phpmailer@synchromedia.co.uk>
 * @author    Andy Prevost
 * @copyright 2012 - 2020 Marcus Bointon
 * @copyright 2004 - 2009 Andy Prevost
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 */

namespace PHPMailer\Test\PHPMailer;

use PHPMailer\Test\TestCase;

/**
 * Test setting the "from" address.
 */
final class SetFromTest extends TestCase
{

    /**
     * Test succesfully setting the From, FromName and Sender properties.
     *
     * @dataProvider dataSetFromSuccess
     *
     * @param string $expected Expected funtion output.
     * @param string $address  Email address input to pass to the function.
     * @param string $name     Optional. Name input to pass to the function.
     */
    public function testSetFromSuccess($expected, $address, $name = '')
    {
        $result = $this->Mail->setFrom($address, $name);
        self::assertTrue($result, 'setFrom failed');

        self::assertSame($expected['From'], $this->Mail->From, 'From has not been set');
        self::assertSame($expected['FromName'], $this->Mail->FromName, 'From name has not been set');
        self::assertSame($expected['From'], $this->Mail->Sender, 'Sender has not been set');
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function dataSetFromSuccess()
    {
        return [
            'Email, no name' => [
                'expected' => [
                    'From'     => 'a@example.com',
                    'FromName' => '',
                ],
                'address'  => 'a@example.com',
            ],
            'Email, no name; whitespace padding around email' => [
                'expected' => [
                    'From'     => 'whitespacepadding@example.com',
                    'FromName' => '',
                ],
                'address'  => " \t  whitespacepadding@example.com   \n",
            ],
            'Email + name' => [
                'expected' => [
                    'From'     => 'a@example.com',
                    'FromName' => 'some name',
                ],
                'address'  => 'a@example.com',
                'name'     => 'some name',
            ],
            'Email + name; quotes in the name' => [
                'expected' => [
                    'From'     => 'bob@example.com',
                    'FromName' => '"Bob\'s Burgers" (Bob\'s "Burgers")',
                ],
                'address'  => 'bob@example.com',
                'name'     => '"Bob\'s Burgers" (Bob\'s "Burgers")',
            ],
            'Email + name; line breaks in name' => [
                'expected' => [
                    'From'     => 'removebreaks@example.com',
                    'FromName' => 'somename',
                ],
                'address'  => 'removebreaks@example.com',
                'name'     => "\r\nsome\r\nname\r\n",
            ],
            'Email + name; whitespace padding around name' => [
                'expected' => [
                    'From'     => 'a@example.com',
                    'FromName' => 'some name',
                ],
                'address'  => 'a@example.com',
                'name'     => "\t\tsome name    \r\n",
            ],
        ];
    }

    /**
     * Test setting the From address, but not overruling the Sender value when the $auto parameter is set to false.
     */
    public function testSetFromDoesNotOverruleSenderWithAutoFalse()
    {
        $result = $this->Mail->setFrom('overruled@example.com', 'some name', false);

        self::assertTrue($result, 'setFrom failed');
        self::assertSame('', $this->Mail->Sender, 'Sender has been overruled');
    }

    /**
     * Test setting the From address, but not overruling an existing, non-empty Sender value.
     */
    public function testSetFromDoesNotOverruleExistingSender()
    {
        $sender             = 'donotoverrule@example.com';
        $this->Mail->Sender = $sender;

        $result = $this->Mail->setFrom('overruled@example.com');

        self::assertTrue($result, 'setFrom failed');
        self::assertSame($sender, $this->Mail->Sender, 'Sender has been overruled');
    }

    /**
     * Test unsuccesfully setting the From, FromName and Sender properties.
     */
    public function testSetFromFail()
    {
        self::assertFalse($this->Mail->setFrom('a@example.com.', 'some name'), 'setFrom accepted invalid address');
    }
}
