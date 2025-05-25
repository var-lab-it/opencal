<?php

declare(strict_types=1);

namespace App\Tests\UnitTests\Command;

use App\Command\CreateUserCommand;
use App\Entity\User;
use App\User\UserService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserCommandTest extends TestCase
{
    use MatchesSnapshots;

    private UserService&MockObject $userServiceMock;
    private UserPasswordHasherInterface&MockObject $userPasswordHasherMock;
    private InputInterface&MockObject $inputMock;
    private OutputInterface&MockObject $outputMock;

    protected function setUp(): void
    {
        $this->userServiceMock        = $this->createMock(UserService::class);
        $this->userPasswordHasherMock = $this->createMock(UserPasswordHasherInterface::class);
        $this->inputMock              = $this->createMock(InputInterface::class);
        $this->outputMock             = $this->createMock(OutputInterface::class);
    }

    public function testConfigure(): void
    {
        $cmd = $this->getCommand();

        $arguments = $cmd->getDefinition()->getArguments();
        self::assertCount(
            4,
            $arguments,
        );

        $argData = [];

        foreach ($arguments as $argument) {
            $argData[] = [
                'name'        => $argument->getName(),
                'is_required' => $argument->isRequired(),
                'default'     => $argument->getDefault(),
                'description' => $argument->getDescription(),
            ];
        }

        self::assertMatchesJsonSnapshot($argData);

        $synopsis = $cmd->getSynopsis();
        self::assertSame(
            'opencal:user:create <email> <given_name> <family_name> [<password>]',
            $synopsis,
        );
    }

    #[DataProvider('provideArgumentsData')]
    public function testExecuteArgumentsValidation(
        string $email,
        string $givenName,
        string $familyName,
        ?string $password = null,
    ): void {
        $cmd      = $this->getCommand();
        $refClass = new \ReflectionClass($cmd);
        $method   = $refClass->getMethod('execute');

        $this->inputMock
            ->method('getArgument')
            ->willReturnCallback(static function ($param) use ($email, $givenName, $familyName, $password) {
                return match ($param) {
                    'email' => $email,
                    'given_name' => $givenName,
                    'family_name' => $familyName,
                    'password' => $password,
                    default => null,
                };
            });

        $result = $method->invokeArgs($cmd, [
            $this->inputMock,
            $this->outputMock,
        ]);

        self::assertSame(
            Command::FAILURE,
            $result,
        );
    }

    public function testExecuteUserEmailInUse(): void
    {
        $cmd      = $this->getCommand();
        $refClass = new \ReflectionClass($cmd);
        $method   = $refClass->getMethod('execute');

        $this->inputMock
            ->method('getArgument')
            ->willReturnCallback(static function ($param) {
                return match ($param) {
                    'email' => 'valid@email.tld',
                    'given_name' => 'Unit',
                    'family_name' => 'Tester',
                    'password' => '12345!',
                    default => null,
                };
            });

        $this->userServiceMock
            ->method('isEmailUsed')
            ->willReturn(true);

        $result = $method->invokeArgs($cmd, [
            $this->inputMock,
            $this->outputMock,
        ]);

        self::assertSame(
            Command::FAILURE,
            $result,
        );
    }

    public function testExecuteSucceedsWithPassword(): void
    {
        $cmd      = $this->getCommand();
        $refClass = new \ReflectionClass($cmd);
        $method   = $refClass->getMethod('execute');

        $this->inputMock
            ->method('getArgument')
            ->willReturnCallback(static function ($param) {
                return match ($param) {
                    'email' => 'valid@email.tld',
                    'given_name' => 'Unit',
                    'family_name' => 'Tester',
                    'password' => '12345!',
                    default => null,
                };
            });

        $this->userServiceMock
            ->method('isEmailUsed')
            ->willReturn(false);

        $userMock = $this->createMock(User::class);
        $this->userServiceMock
            ->method('createUser')
            ->willReturn($userMock);

        $this->userPasswordHasherMock
            ->expects(self::once())
            ->method('hashPassword');

        $result = $method->invokeArgs($cmd, [
            $this->inputMock,
            $this->outputMock,
        ]);

        self::assertSame(
            Command::SUCCESS,
            $result,
        );
    }

    public function testExecuteSucceedsWithNullPassword(): void
    {
        $cmd      = $this->getCommand();
        $refClass = new \ReflectionClass($cmd);
        $method   = $refClass->getMethod('execute');

        $this->inputMock
            ->method('getArgument')
            ->willReturnCallback(static function ($param) {
                return match ($param) {
                    'email' => 'valid@email.tld',
                    'given_name' => 'Unit',
                    'family_name' => 'Tester',
                    'password' => null,
                    default => null,
                };
            });

        $this->userServiceMock
            ->method('isEmailUsed')
            ->willReturn(false);

        $userMock = $this->createMock(User::class);
        $this->userServiceMock
            ->method('createUser')
            ->willReturn($userMock);

        $this->userPasswordHasherMock
            ->expects(self::never())
            ->method('hashPassword');

        $result = $method->invokeArgs($cmd, [
            $this->inputMock,
            $this->outputMock,
        ]);

        self::assertSame(
            Command::SUCCESS,
            $result,
        );
    }

    /** @return array<int, array<string, string|null>> */
    public static function provideArgumentsData(): array
    {
        return [
            [
                'email'      => 'invalid',
                'givenName'  => 'Unit',
                'familyName' => 'Tester',
                'password'   => null,
            ],
            [
                'email'      => 'valid@email.tld',
                'givenName'  => 'Unit',
                'familyName' => 'Tester',
                'password'   => '1234',
            ],
            [
                'email'      => 'valid@email.tld',
                'givenName'  => '',
                'familyName' => 'Tester',
                'password'   => null,
            ],
            [
                'email'      => 'valid@email.tld',
                'givenName'  => 'Unit',
                'familyName' => '',
                'password'   => null,
            ],
            [
                'email'      => 'valid@email.tld',
                'givenName'  => '',
                'familyName' => '',
                'password'   => null,
            ],
        ];
    }

    private function getCommand(): CreateUserCommand
    {
        return new CreateUserCommand(
            $this->userServiceMock,
            $this->userPasswordHasherMock,
        );
    }
}
