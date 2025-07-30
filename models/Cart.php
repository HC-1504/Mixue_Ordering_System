<?php

class Cart
{
    /**
     * Ensure session is started
     */
    public static function start()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Get all items in the cart
     */
    public static function get(): array
    {
        self::start();
        return $_SESSION['cart'] ?? [];
    }

    /**
     * Add a new item to the cart
     */
    public static function add(array $item): void
    {
        self::start();
        $_SESSION['cart'][] = $item;
    }

    /**
     * Update a specific item in the cart
     */
    public static function update(int|string $index, array $data): void
    {
        self::start();

        if (isset($_SESSION['cart'][$index])) {
            $_SESSION['cart'][$index] = array_merge($_SESSION['cart'][$index], $data);
        }
    }

    /**
     * Remove an item from the cart by index
     */
    public static function remove(int|string $index): void
    {
        self::start();

        if (isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // reindex
        }
    }

    /**
     * Clear the entire cart
     */
    public static function clear(): void
    {
        self::start();
        unset($_SESSION['cart']);
    }

    /**
     * Count total items in the cart
     */
    public static function count(): int
    {
        self::start();
        return count($_SESSION['cart'] ?? []);
    }
}
