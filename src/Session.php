<?php declare(strict_types=1);
/**
 * MIT License
 * 
 * Copyright (c) 2022 Nicholas English
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Zypto\Session;

use ArrayAccess;

class Session implements ArrayAccess, SessionInterface
{
    /** @var string $sessionName The sessions name. */
    private string $sessionName;
    
    /**
     * Construct a new session interface.
     *
     * @param \Zypto\Session\SessionManager The session manager.
     *
     * @return never Returns nothing.
     */
    public function __construct(
        public SessionManager $sessionManager
    ) {
        $this->setName();
    }

    /**
     * Set the session name.
     *
     * @param string $sessionName The session name to set.
     *
     * @throws \RuntimeException If the session has an invalid state.
     *
     * @return void Returns nothing.
     */
    public function initialize(bool $populateSessionGlobal = true): void
    {
        $this->sessionManager->resume($this->sessionName, $populateSessionGlobal);
    }

    /**
     * Regenerate the session by generating a new session id.
     *
     * @return void Returns nothing.
     */
    public function regenerate(bool $destoryOldSession = false): void
    {
        $this->sessionManager->regenerateId($destroyOldSession);
    }

    /**
     * Invalidates a currently running session.
     *
     * @return void Returns nothing.
     */
    public function invalidate(): void
    {
        $this->regenerate();
        $this->sessionManager->emptyContents();
    }

    /**
     * Set the session name.
     *
     * @param string $sessionName The session name to set.
     *
     * @return void Returns nothing.
     */
    public function setName(string $sessionName = 'ZyptoSession'): void
    {
        $this->sessionName = $sessionName;
    }

    /**
     * Set a session variable.
     *
     * @param mixed A key and a value to set.
     *
     * @return void Returns nothing.
     */
    public function offsetSet(mixed $offset, mixed $value) {
        if (\is_null($offset)) {
            throw new InvalidArgumentException('Array push is not allowed.');
        }
        $this->set($offset, $value);
    }

    /**
     * Check a session variable to see if it exists.
     *
     * @param mixed $key A key to check.
     *
     * @return bool Returns true if the session variable key exists and false if not.
     */
    public function offsetExists(mixed $offset)
    {
        return $this->has($offset);
    }

    /**
     * Delete a session variable.
     *
     * @param mixed A key to delete.
     *
     * @return void Returns nothing.
     */
    public function offsetUnset(mixed $offset)
    {
        $this->del($offset);
    }

    /**
     * Get a session variable or variables.
     *
     * @param mixed $key A key or an array of keys to get.
     *
     * @return mixed Returns the value of a session variable.
     */
    public function offsetGet(mixed $offset)
    {
        return $this->get($offset);
    }

    /**
     * Check a session variable or variables to see if they exist.
     *
     * @param mixed $key A key or an array of keys to check.
     *
     * @return bool|array Returns true if the session variable key exists and false if not or
     *                    an associative array of keys and whether or not they exist.
     */
    public function has(mixed $key): bool|array
    {
        return $this->sessionManager->has($key);
    }

    /**
     * Get a session variable or variables.
     *
     * @param mixed $key           A key or an array of keys to get.
     * @param mixed $defaultValue  A default value to return.
     * @param array $defaultValues An associative array and keys and default values
     *                             to use.
     *
     * @return mixed Returns the value of a session variable or an associative
     *               array of keys and value.
     */
    public function get(mixed $key, mixed $defualtValue = null, array $defaultValues = []): mixed
    {
        return $this->sessionManager->retrieve($key, $defualtValue, $defaultValues);
    }

    /**
     * Delete a session variable or variables.
     *
     * @param mixed A key or an array of keys to delete.
     *
     * @return void Returns nothing.
     */
    public function del(mixed $key): void
    {
        $this->sessionManager->remove($key);
    }

    /**
     * Set a session variable or variables.
     *
     * @param mixed A key or an associative array of keys and values to set.
     *
     * @return void Returns nothing.
     */
    public function set(mixed $key, ?mixed $value = null): void
    {
        $this->sessionManager->add($key, $value);
    }
}
