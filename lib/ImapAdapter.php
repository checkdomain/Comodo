<?php

namespace Checkdomain\Comodo;

/**
 * @method void                        appendMessage(string $message, $folder = null, array $flags = null)
 * @method void                        close()
 * @method void                        copyMessage(int $id, $folder)
 * @method int                         count()
 * @method int                         countMessages($flags = null)
 * @method void                        createFolder(string $name, $parentFolder = null)
 * @method \Laminas\Mail\Storage\Message  current()
 * @method array                       getCapabilities()
 * @method \Laminas\Mail\Storage\Folder   getCurrentFolder()
 * @method \Laminas\Mail\Storage\Folder   getFolders($rootFolder = null)
 * @method \Laminas\Mail\Storage\Message  getMessage(int $id)
 * @method int                         getNumberByUniqueId(string $id)
 * @method array|string                getRawContent(int $id, $part = null)
 * @method array|string                getRawHeader(int $id, $part = null, int $topLines = 0)
 * @method array|int                   getSize(int $id)
 * @method array|string                getUniqueId(int $id = null)
 * @method int                         key()
 * @method void                        moveMessage(int $id, $folder)
 * @method void                        next()
 * @method void                        noop()
 * @method bool                        offsetExists(int $int)
 * @method int                         offsetGet(int $id)
 * @method void                        offsetSet(int $id)
 * @method bool                        offsetUnset(int $id)
 * @method void                        removeFolder($name)
 * @method void                        removeMessage(int $id)
 * @method void                        renameFolder($oldName, string $newName)
 * @method void                        rewind()
 * @method void                        seek(int $pos)
 * @method void                        selectFolder($globalName)
 * @method void                        setFlags(int $id, array $flags)
 * @method bool                        valid()
 * @method array                       search(array $params)
 *
 * @package Checkdomain\Comodo
 */
class ImapAdapter
{
    /**
     * @var array
     */
    protected $params;

    /**
     * @var null|ImapExtension
     */
    protected $instance;

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * @return ImapExtension
     */
    public function getInstance()
    {
        if ($this->instance) {
            return $this->instance;
        }

        return new ImapExtension($this->params);
    }

    /**
     * @param ImapExtension $instance
     *
     * @return $this
     */
    public function setInstance(ImapExtension $instance)
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->getInstance(), $name], $arguments);
    }
}
