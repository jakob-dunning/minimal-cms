<?php

use App\Entity\Page;
use App\Exception\PageNotFoundException;
use App\Repository\PageRepository;
use App\Service\Database\MariaDbService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Repository\PageRepository
 * @uses   \App\Service\Database\MariaDbService
 * @uses   \App\Service\Database\RelationalDatabaseInterface
 * @uses   \App\Service\Response\ResponseInterface
 * @uses   \App\Service\Response\Response
 * @uses   \App\ValueObject\Uri
 */
class PageRepositoryTest extends TestCase
{
    private PageRepository $pageRepository;

    private MockObject $databaseMock;

    private array $defaultPageData;

    public function setUp(): void
    {
        parent::setUp();

        $this->databaseMock    = $this->createMock(MariaDbService::class);
        $this->pageRepository  = new PageRepository($this->databaseMock);
        $this->defaultPageData = $pageData = [
            'id'      => 5,
            'uri'     => 'sdfsdf',
            'title'   => 'ingo',
            'content' => 'ulishfophefou',
        ];
    }

    public function testFindByPathReturnsPage()
    {
        $this->databaseMock->expects($this->once())
                           ->method('select')
                           ->with(['*'], 'page', ['uri' => $this->defaultPageData['uri']])
                           ->willReturn([$this->defaultPageData]);

        $page = $this->pageRepository->findByPath($this->defaultPageData['uri']);

        $this->assertSame($page->getId(), $this->defaultPageData['id']);
        $this->assertSame((string)$page->getUri(), $this->defaultPageData['uri']);
        $this->assertSame($page->getContent(), $this->defaultPageData['content']);
        $this->assertSame($page->getTitle(), $this->defaultPageData['title']);
    }

    public function testFindByPathThrowsUnknownPageException()
    {
        $this->databaseMock->expects($this->once())
                           ->method('select')
                           ->with(['*'], 'page', ['uri' => $this->defaultPageData['uri']])
                           ->willReturn([]);

        $this->expectException(PageNotFoundException::class);

        $this->pageRepository->findByPath($this->defaultPageData['uri']);
    }

    public function testFindByIdReturnsPage()
    {
        $this->databaseMock->expects($this->once())
                           ->method('select')
                           ->with(['*'], 'page', ['id' => $this->defaultPageData['id']])
                           ->willReturn([$this->defaultPageData]);

        $page = $this->pageRepository->findById($this->defaultPageData['id']);

        $this->assertSame($page->getId(), $this->defaultPageData['id']);
        $this->assertSame((string)$page->getUri(), $this->defaultPageData['uri']);
        $this->assertSame($page->getContent(), $this->defaultPageData['content']);
        $this->assertSame($page->getTitle(), $this->defaultPageData['title']);
    }

    public function testFindByIdThrowsUnknownPageException()
    {
        $this->databaseMock->expects($this->once())
                           ->method('select')
                           ->with(['*'], 'page', ['id' => $this->defaultPageData['id']])
                           ->willReturn([]);

        $this->expectException(PageNotFoundException::class);

        $this->pageRepository->findById($this->defaultPageData['id']);
    }

    public function testFindAll()
    {
        $pageData = [
            [
                'id'      => 5,
                'uri'     => 'sdfsdf',
                'title'   => 'ingo',
                'content' => 'ulishfophefou',
            ],
            [
                'id'      => 8,
                'uri'     => 'sdfsasdasddf',
                'title'   => 'inasdasdgo',
                'content' => 'ulishfddasdasdasdaophefou',
            ],
        ];
        $this->databaseMock->expects($this->once())
                           ->method('select')
                           ->with(['*'], 'page')
                           ->willReturn($pageData);

        $pages = $this->pageRepository->findAll();

        /** @var Page $page */
        $page = $pages[0];
        /** @var Page $page2 */
        $page2 = $pages[1];

        $this->assertSame($page->getId(), $pageData[0]['id']);
        $this->assertSame((string)$page->getUri(), $pageData[0]['uri']);
        $this->assertSame($page->getContent(), $pageData[0]['content']);
        $this->assertSame($page->getTitle(), $pageData[0]['title']);

        $this->assertSame($page2->getId(), $pageData[1]['id']);
        $this->assertSame((string)$page2->getUri(), $pageData[1]['uri']);
        $this->assertSame($page2->getContent(), $pageData[1]['content']);
        $this->assertSame($page2->getTitle(), $pageData[1]['title']);
    }

    public function testCreate()
    {
        $uri     = 'sdfsasdasddf';
        $title   = 'inasdasdgo';
        $content = 'ulishfddasdasdasdaophefou';
        $this->databaseMock->expects($this->once())
                           ->method('insert')
                           ->with('page', ['uri' => $uri, 'title' => $title, 'content' => $content]);

        $this->pageRepository->create($uri, $title, $content);
    }

    public function testDeleteById()
    {
        $id = 9;
        $this->databaseMock->expects($this->once())
                           ->method('delete')
                           ->with('page', ['id' => $id]);

        $this->pageRepository->deleteById($id);
    }

    public function testPersist()
    {
        $this->databaseMock->expects($this->once())
                           ->method('update')
                           ->with(
                               'page',
                               [
                                   'uri'     => $this->defaultPageData['uri'],
                                   'title'   => $this->defaultPageData['title'],
                                   'content' => $this->defaultPageData['content'],
                               ],
                               ['id' => $this->defaultPageData['id']]
                           );

        $this->pageRepository->persist(Page::createFromArray($this->defaultPageData));
    }
}