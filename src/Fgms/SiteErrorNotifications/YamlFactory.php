<?php

namespace Fgms\SiteErrorNotifications;

/**
 * Creates an @ref ErrorHandlerInterface instance
 * from a YAML configuration document.
 */
class YamlFactory
{
    private static function join($path, $key)
    {
        //  TODO: JSON Pointer escaping on $key
        return sprintf('%s/%s',$path,$key);
    }

    private static function raiseMissing($path, $key)
    {
        throw new InvalidYamlException(
            sprintf(
                'Expected "%s"',
                self::join($path,$key)
            )
        );
    }

    private static function raiseTypeMismatch($path, $key, $expected)
    {
        throw new InvalidYamlException(
            sprintf(
                'Expected "%s" to be %s',
                self::join($path,$key),
                $expected
            )
        );
    }

    private static function getOrNull(array $arr, $path, $key)
    {
        if (!isset($arr[$key])) return null;
        return $arr[$key];
    }

    private static function get(array $arr, $path, $key)
    {
        $retr = self::getOrNull($arr,$path,$key);
        if (is_null($retr)) self::raiseMissing($path,$key);
        return $retr;
    }

    private static function getArrayOrNull(array $arr, $path, $key)
    {
        if (!isset($arr[$key])) return null;
        $retr = $arr[$key];
        if (!is_array($retr)) self::raiseTypeMismatch($path,$key,'array');
        return $retr;
    }

    private static function getArray(array $arr, $path, $key)
    {
        $retr = self::getArrayOrNull($arr,$path,$key);
        if (is_null($retr)) self::raiseMissing($path,$key);
        return $retr;
    }

    private static function getStringOrNull(array $arr, $path, $key)
    {
        if (!isset($arr[$key])) return null;
        $retr = $arr[$key];
        if (!is_string($retr)) self::raiseTypeMismatch($path,$key,'string');
        return $retr;
    }

    private static function getString(array $arr, $path, $key)
    {
        $retr = self::getStringOrNull($arr,$path,$key);
        if (is_null($retr)) self::raiseMissing($path,$key);
        return $retr;
    }

    private static function getBooleanOrNull(array $arr, $path, $key)
    {
        if (!isset($arr[$key])) return null;
        $retr = $arr[$key];
        if (!is_bool($retr)) self::raiseTypeMismatch($path,$key,'boolean');
        return $retr;
    }

    private static function getBoolean(array $arr, $path, $key)
    {
        $retr = self::getBooleanOrNull($arr,$path,$key);
        if (is_null($retr)) self::raiseMissing($path,$key);
        return $retr;
    }

    private static function getEmailsOrNull(array $arr, $path, $key)
    {
        $val = self::getOrNull($arr,$path,$key);
        if (is_null($val)) return null;
        if (is_string($val)) return [$val];
        if (is_array($val)) {
            $i = 0;
            foreach ($val as $addr) {
                if (!is_string($addr)) self::raiseTypeMismatch(self::join($path,$key),$i,'string');
                ++$i;
            }
            return $val;
        }
        throw new InvalidYamlException(
            sprintf(
                'Expected "%s" to be string or array',
                self::join($path,$key)
            )
        );
    }

    private static function getEmails(array $arr, $path, $key)
    {
        $retr = self::getEmailsOrNull($arr,$path,$key);
        if (is_null($retr)) self::raiseMissing($path,$key);
        return $retr;
    }

    private static function createTwig(array $arr, $path)
    {
        return new \Twig_Environment(
            new \Twig_Loader_Filesystem(
                self::getString($arr,$path,'templates'),
                ['strict_variables' => true]
            )
        );
    }

    private static function createEmail(array $arr, $path)
    {
        $msg = new \Swift_Message();
        $msg->setFrom(self::getString($arr,$path,'from'))
            ->setTo(self::getEmails($arr,$path,'to'));
        $swift = \Swift_Mailer::newInstance(
            \Swift_MailTransport::newInstance()
        );
        return new EmailErrorHandler(
            $msg,
            $swift,
            self::createTwig($arr,$path),
            self::getStringOrNull($arr,$path,'name')
        );
    }

    private static function createMonolog(array $arr, $path)
    {
        $file = self::getString($arr,$path,'file');
        $name = self::getStringOrNull($arr,$path,'name');
        if (is_null($name)) $name = 'Fg Error';
        $log = new \Monolog\Logger($name);
        $stream = new \Monolog\Handler\StreamHandler($file,\Monolog\Logger::ERROR);
        $stream->setFormatter(new \Monolog\Formatter\LineFormatter(null,null,true));
        $log->pushHandler($stream);
        return new Psr3ErrorHandler($log);
    }

    private static function createHtml(array $arr, $path)
    {
        $twig = self::createTwig($arr,$path);
        $debug = self::getBooleanOrNull($arr,$path,'debug');
        //  Err on the side of caution and assume we're not in
        //  debug mode
        if (is_null($debug)) $debug = false;
        if ($debug) return new DebugHtmlErrorHandler($twig);
        return new ProductionHtmlErrorHandler($twig);
    }

    private static function createFromArray(array $arr)
    {
        $retr = new CompositeErrorHandler();
        $email = self::getArrayOrNull($arr,'','email');
        if (!is_null($email)) $retr->add(self::createEmail($email,'/email'));
        $monolog = self::getArrayOrNull($arr,'','monolog');
        if (!is_null($monolog)) $retr->add(self::createMonolog($monolog,'/monolog'));
        $die = new CompositeErrorHandler();
        $retr->add(
            new IgnoreErrorHandler(
                E_WARNING|E_NOTICE|E_CORE_WARNING|E_COMPILE_WARNING|E_USER_WARNING|E_USER_NOTICE|E_STRICT|E_DEPRECATED|E_USER_DEPRECATED,
                $die
            )
        );
        $die->add(new InternalServerErrorErrorHandler());
        $html = self::getArrayOrNull($arr,'','html');
        if (!is_null($html)) $die->add(self::createHtml($html,'/html'));
        $die->add(new DieErrorHandler());
        return new AtOperatorErrorHandler($retr);
    }

    /**
     * Creates an @ref ErrorHandlerInterface instance from
     * a YAML configuration file.
     *
     * @param string $yaml
     *  A YAML document.
     *
     * @return ErrorHandlerInterface
     */
    public static function create($yaml)
    {
        try {
            $value = \Symfony\Component\Yaml\Yaml::parse($yaml);
        } catch (\Symfony\Component\Yaml\Exception\ParseException $ex) {
            throw new InvalidYamlException(
                sprintf(
                    'Failed to parse YAML string: %s',
                    $ex->getMessage()
                ),
                $ex->getCode(),
                $ex
            );
        }
        if (!is_array($value)) throw new InvalidYamlException(
            'Expected root of YAML document to be array'
        );
        return self::createFromArray($value);
    }
}
