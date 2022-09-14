<?php

namespace App\Command;

use App\Entity\Post;
use App\Entity\User;
use App\Services\EntityService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:posts',
    description: 'Parse and save posts',
    aliases: ['app:posts'],
    hidden: false
)]
class ParseAdnSavePostsCommand extends Command
{
    public function __construct(public EntityService $postService, public ManagerRegistry $doctrine)
    {
        parent::__construct();
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @throws \Throwable
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $parsedUsers = $this->postService->getData('users');
        $parsedPosts = $this->postService->getData('posts');
        $entityManager = $this->doctrine->getManager();

        $this->doctrine->getConnection()->beginTransaction();
        try {
            foreach ($parsedUsers as $parsedUser) {
                $user = new User();
                $user->setFirstName($parsedUser['name']);
                $user->setLastName($parsedUser['username']);
                $entityManager->persist($user);
            }
            $entityManager->flush();

            foreach ($parsedPosts as $parsedPost) {
                $user = $entityManager->getRepository(User::class)->find($parsedPost['userId']);
                $post = new Post();
                $post->setTitle($parsedPost['title']);
                $post->setBody($parsedPost['body']);
                $post->setUser($user);
                $post->setUserId($user->getId());
                $entityManager->persist($post);
            }
            $entityManager->flush();

            $this->doctrine->getConnection()->commit();
        } catch (\Throwable $e) {
            $this->doctrine->getConnection()->rollBack();
            throw $e;
        }

        return Command::SUCCESS;
    }
}
