<?php

use App\Entity\User\User;
use App\Exception\UserNotFoundException;
use App\Repository\UserRepository;
use App\Service\Database\MariaDbService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Repository\UserRepository
 * @uses   \App\Service\Database\MariaDbService
 * @uses   \App\Service\Database\RelationalDatabaseInterface
 * @uses   \App\Model\Response\ResponseInterface
 * @uses   \App\Model\Response\Response
 */
class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;

    private MockObject $databaseMock;

    private array $defaultUserData;

    public function setUp(): void
    {
        parent::setUp();

        $this->databaseMock    = $this->createMock(MariaDbService::class);
        $this->userRepository  = new UserRepository($this->databaseMock);
        $this->defaultUserData = [
            'id'                 => 2,
            'username'           => 'ingo',
            'password'           => 'ulishfophefou',
            'session_id'         => 'juhsgfphupfh',
            'session_expires_at' => '2021-08-21 19:09:10',
        ];
    }

    public function testFindBySessionIdReturnsUser()
    {
        $this->databaseMock->expects($this->once())
                           ->method('select')
                           ->with(['*'], 'user', ['session_id' => $this->defaultUserData['session_id']])
                           ->willReturn([$this->defaultUserData]);

        $user = $this->userRepository->findBySessionId($this->defaultUserData['session_id']);

        $this->assertSame($user->getUserName(), $this->defaultUserData['username']);
        $this->assertSame($user->getId(), $this->defaultUserData['id']);
        $this->assertSame($user->getPassword(), $this->defaultUserData['password']);
        $this->assertSame(
            $user->getSessionExpiresAt()->format('Y-m-d H:i:s'),
            (new \DateTime($this->defaultUserData['session_expires_at']))->format('Y-m-d H:i:s')
        );
        $this->assertSame($user->getSessionId(), $this->defaultUserData['session_id']);
    }

    public function testFindBySessionIdThrowsUnknownUserException()
    {
        $this->databaseMock->expects($this->once())
                           ->method('select')
                           ->with(['*'], 'user', ['session_id' => $this->defaultUserData['session_id']])
                           ->willReturn([]);

        $this->expectException(UserNotFoundException::class);

        $this->userRepository->findBySessionId($this->defaultUserData['session_id']);
    }

    public function testFindByUsernameReturnsUser()
    {
        $this->databaseMock->expects($this->once())
                           ->method('select')
                           ->with(['*'], 'user', ['username' => $this->defaultUserData['username']])
                           ->willReturn([$this->defaultUserData]);

        $user = $this->userRepository->findByUsername($this->defaultUserData['username']);

        $this->assertSame($user->getUserName(), $this->defaultUserData['username']);
        $this->assertSame($user->getId(), $this->defaultUserData['id']);
        $this->assertSame($user->getPassword(), $this->defaultUserData['password']);
        $this->assertSame(
            $user->getSessionExpiresAt()->format('Y-m-d H:i:s'),
            (new \DateTime($this->defaultUserData['session_expires_at']))->format('Y-m-d H:i:s')
        );
        $this->assertSame($user->getSessionId(), $this->defaultUserData['session_id']);
    }

    public function testFindByUsernameThrowsUnknownUserException()
    {
        $this->databaseMock->expects($this->once())
                           ->method('select')
                           ->with(['*'], 'user', ['username' => $this->defaultUserData['username']])
                           ->willReturn([]);

        $this->expectException(UserNotFoundException::class);

        $this->userRepository->findByUsername($this->defaultUserData['username']);
    }

    public function testFindByIdReturnsUser()
    {
        $this->databaseMock->expects($this->once())
                           ->method('select')
                           ->with(['*'], 'user', ['id' => $this->defaultUserData['id']])
                           ->willReturn([$this->defaultUserData]);

        $user = $this->userRepository->findById($this->defaultUserData['id']);

        $this->assertSame($user->getUserName(), $this->defaultUserData['username']);
        $this->assertSame($user->getId(), $this->defaultUserData['id']);
        $this->assertSame($user->getPassword(), $this->defaultUserData['password']);
        $this->assertSame(
            $user->getSessionExpiresAt()->format('Y-m-d H:i:s'),
            (new \DateTime($this->defaultUserData['session_expires_at']))->format('Y-m-d H:i:s')
        );
        $this->assertSame($user->getSessionId(), $this->defaultUserData['session_id']);
    }

    public function testFindByIdThrowsUnknownUserException()
    {
        $this->databaseMock->expects($this->once())
                           ->method('select')
                           ->with(['*'], 'user', ['id' => $this->defaultUserData['id']])
                           ->willReturn([]);

        $this->expectException(UserNotFoundException::class);

        $this->userRepository->findById($this->defaultUserData['id']);
    }

    public function testFindAll()
    {
        $userData = [
            [
                'id'                 => 2,
                'username'           => 'ingo',
                'password'           => 'ulishfophefou',
                'session_id'         => 'juhsgfphupfh',
                'session_expires_at' => '2021-08-21 19:09:10',
            ],
            [
                'id'                 => 3,
                'username'           => 'bingo',
                'password'           => 'uasdlishfophefou',
                'session_id'         => 'juhsasdgfphupfh',
                'session_expires_at' => '2021-09-21 19:09:10',
            ],
        ];
        $this->databaseMock->expects($this->once())
                           ->method('select')
                           ->with(['*'], 'user')
                           ->willReturn($userData);

        $users = $this->userRepository->findAll();

        /** @var User $user */
        $user = $users[0];
        /** @var User $user2 */
        $user2 = $users[1];

        $this->assertSame($user->getUserName(), $userData[0]['username']);
        $this->assertSame($user->getId(), $userData[0]['id']);
        $this->assertSame($user->getPassword(), $userData[0]['password']);
        $this->assertSame(
            $user->getSessionExpiresAt()->format('Y-m-d H:i:s'),
            (new \DateTime($userData[0]['session_expires_at']))->format('Y-m-d H:i:s')
        );
        $this->assertSame($user->getSessionId(), $userData[0]['session_id']);

        $this->assertSame($user2->getUserName(), $userData[1]['username']);
        $this->assertSame($user2->getId(), $userData[1]['id']);
        $this->assertSame($user2->getPassword(), $userData[1]['password']);
        $this->assertSame(
            $user2->getSessionExpiresAt()->format('Y-m-d H:i:s'),
            (new \DateTime($userData[1]['session_expires_at']))->format('Y-m-d H:i:s')
        );
        $this->assertSame($user2->getSessionId(), $userData[1]['session_id']);
    }

    public function testCreate()
    {
        $username = 'hansen';
        $password = 'kuhaiohduasdh';
        $this->databaseMock->expects($this->once())
                           ->method('insert')
                           ->with('user', ['username' => $username, 'password' => $password]);

        $this->userRepository->create($username, $password);
    }

    public function testDeleteById()
    {
        $id = 9;
        $this->databaseMock->expects($this->once())
                           ->method('delete')
                           ->with('user', ['id' => $id]);

        $this->userRepository->deleteById($id);
    }

    public function testPersist()
    {
        $this->databaseMock->expects($this->once())
                           ->method('update')
                           ->with(
                               'user',
                               [
                                   'session_id'         => $this->defaultUserData['session_id'],
                                   'session_expires_at' => $this->defaultUserData['session_expires_at'],
                                   'password'           => $this->defaultUserData['password'],
                               ],
                               ['id' => $this->defaultUserData['id']]
                           );

        $this->userRepository->persist(User::createFromArray($this->defaultUserData));
    }

    public function testPersistWithEmptySessionExpiry()
    {
        $this->defaultUserData['session_expires_at'] = null;
        $this->databaseMock->expects($this->once())
                           ->method('update')
                           ->with(
                               'user',
                               [
                                   'session_id'         => $this->defaultUserData['session_id'],
                                   'session_expires_at' => null,
                                   'password'           => $this->defaultUserData['password'],
                               ],
                               ['id' => $this->defaultUserData['id']]
                           );

        $this->userRepository->persist(User::createFromArray($this->defaultUserData));
    }
}