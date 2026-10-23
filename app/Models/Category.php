<?php
declare(strict_types=1);

namespace App\Models;

use PDO;

final class Category
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function forSelect(): array
    {
        return $this->pdo->query('SELECT id, name FROM categories ORDER BY id ASC')->fetchAll();
    }

    public function search(string $keyword = '', string $status = 'all'): array
    {
        $sql = 'SELECT c.*, COUNT(p.id) AS post_count FROM categories c LEFT JOIN posts p ON p.category_id = c.id WHERE 1=1';
        $parameters = [];
        if ($keyword !== '') {
            // Prepare thật (EMULATE_PREPARES=false) không cho dùng lại một
            // placeholder, nên mỗi cột cần một tên riêng.
            $sql .= ' AND (c.name LIKE :name OR c.slug LIKE :slug)';
            $value = '%' . $keyword . '%';
            $parameters += ['name' => $value, 'slug' => $value];
        }
        if (in_array($status, ['active', 'hidden'], true)) {
            $sql .= ' AND c.status = :status';
            $parameters['status'] = $status;
        }
        $sql .= ' GROUP BY c.id, c.name, c.slug, c.description, c.status, c.created_at ORDER BY c.id DESC';
        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT c.*, COUNT(p.id) AS post_count FROM categories c
             LEFT JOIN posts p ON p.category_id = c.id WHERE c.id = ?
             GROUP BY c.id, c.name, c.slug, c.description, c.status, c.created_at'
        );
        $statement->execute([$id]);
        return $statement->fetch() ?: null;
    }

    public function create(array $values): bool
    {
        $statement = $this->pdo->prepare('INSERT INTO categories (name, slug, description, status) VALUES (?, ?, ?, ?)');
        return $statement->execute([$values['name'], $values['slug'], $values['description'], $values['status']]);
    }

    public function update(int $id, array $values): bool
    {
        $statement = $this->pdo->prepare('UPDATE categories SET name = ?, slug = ?, description = ?, status = ? WHERE id = ?');
        return $statement->execute([$values['name'], $values['slug'], $values['description'], $values['status'], $id]);
    }

    public function delete(int $id): bool
    {
        $statement = $this->pdo->prepare('DELETE FROM categories WHERE id = ?');
        return $statement->execute([$id]);
    }
}
