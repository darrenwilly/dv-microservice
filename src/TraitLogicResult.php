<?php
namespace DV\MicroService;

use ArrayAccess ;
use Traversable ;
use Zend\Stdlib\ArrayUtils;
use InvalidArgumentException ;

trait TraitLogicResult
{

    /**
     * View variables
     * @var array|ArrayAccess&Traversable
     */
    protected $variables = [];
    protected $logicResult ;

    public function getLogicResult() : LogicResult
    {
        return $this->logicResult ;
    }

    public function setLogicResult($logicResult)
    {
        $this->logicResult = $logicResult ;
    }

    /**
     * Get a single view variable
     *
     * @param  string       $name
     * @param  mixed|null   $default (optional) default value if the variable is not present.
     * @return mixed
     */
    public function getVariable($name, $default = null)
    {
        $name = (string) $name;
        if (array_key_exists($name, $this->variables)) {
            return $this->variables[$name];
        }

        return $default;
    }

    /**
     * Set view variable
     *
     * @param  string $name
     * @param  mixed $value
     * @return parent
     */
    public function setVariable($name, $value)
    {
        $this->variables[(string) $name] = $value;
        return $this;
    }

    /**
     * Set view variables en masse
     *
     * Can be an array or a Traversable + ArrayAccess object.
     *
     * @param  array|ArrayAccess|Traversable $variables
     * @param  bool $overwrite Whether or not to overwrite the internal container with $variables
     * @throws \InvalidArgumentException
     * @return parent
     */
    public function setVariables($variables, $overwrite = false)
    {
        if (! is_array($variables) && ! $variables instanceof Traversable) {
            throw new InvalidArgumentException(sprintf(
                '%s: expects an array, or Traversable argument; received "%s"',
                __METHOD__,
                (is_object($variables) ? get_class($variables) : gettype($variables))
            ));
        }

        if ($overwrite) {
            if (is_object($variables) && ! $variables instanceof ArrayAccess) {
                $variables = ArrayUtils::iteratorToArray($variables);
            }

            $this->variables = $variables;
            return $this;
        }

        foreach ($variables as $key => $value) {
            $this->setVariable($key, $value);
        }

        return $this;
    }

    public function getVariables()
    {
        return $this->variables ;
    }
}