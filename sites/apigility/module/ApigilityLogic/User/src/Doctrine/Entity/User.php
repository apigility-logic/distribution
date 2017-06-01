<?php

namespace ApigilityLogic\User\Doctrine\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Table;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;

use ZF\OAuth2\Doctrine\Entity\UserInterface;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * Class User
 * @package ApigilityLogic\User\DoctrineEntity
 * @Entity @Table(name="al_user_user")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 */
class User implements UserInterface, ArraySerializableInterface
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    protected $client;
    protected $accessToken;
    protected $authorizationCode;
    protected $refreshToken;

    // OpenID fields
    protected $username;
    protected $password;
    protected $profile;
    protected $email;
    protected $country;
    protected $phone_number;

    public function exchangeArray(array $data)
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'username':
                    $this->setUsername($value);
                    break;
                case 'password':
                    $this->setPassword($value);
                    break;
                case 'profile':
                    $this->setProfile($value);
                    break;
                case 'email':
                    $this->setEmail($value);
                    break;
                case 'country':
                    $this->setAddress($value);
                    break;
                case 'phone_number':
                case 'phoneNumber':
                    $this->setPhone($value);
                    break;
                default:
                    break;
            }
        }

        return $this;
    }

    public function getArrayCopy()
    {
        return array(
            'id' => $this->getId(),
            'username' => $this->getUsername(),
            'password' => $this->getPassword(),
            'profile' => $this->getProfile(),
            'email' => $this->getEmail(),
            'country' => $this->getCountry(),
            'phone_number' => $this->getPhoneNumber(), // underscore formatting for openid
            'phoneNumber' => $this->getPhoneNumber(),
        );
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPhoneNumber()
    {
        return $this->phone_number;
    }

    public function setPhoneNumber($value)
    {
        $this->phone_number = $value;

        return $this;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function setCountry($value)
    {
        $this->country = $value;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($value)
    {
        $this->email = $value;

        return $this;
    }

    public function getProfile()
    {
        return $this->profile;
    }

    public function setProfile($value)
    {
        $this->profile = $value;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($value)
    {
        $this->username = $value;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($value)
    {
        $this->password = $value;

        return $this;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getAuthorizationCode()
    {
        return $this->authorizationCode;
    }

    public function getRefreshToken()
    {
        return $this->refreshToken;
    }
}
