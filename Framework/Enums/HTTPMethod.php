<?php

namespace Framework\Enums;

/**
 * Enum HTTPMethod
 *
 * This enumeration represents different HTTP methods that a request can use.
 */
enum HTTPMethod: string {
    /** @var string GET method requests a representation of the specified resource. Requests using GET should only retrieve data. */
    case GET = "GET";

    /** @var string HEAD method asks for a response identical to a GET request, but without the response body. */
    case HEAD = "HEAD";

    /** @var string POST method submits an entity to the specified resource, often causing a change in state or side effects on the server. */
    case POST = "POST";

    /** @var string PUT method replaces all current representations of the target resource with the request payload. */
    case PUT = "PUT";

    /** @var string DELETE method deletes the specified resource. */
    case DELETE = "DELETE";

    /** @var string CONNECT method establishes a tunnel to the server identified by the target resource. */
    case CONNECT = "CONNECT";

    /** @var string OPTIONS method describes the communication options for the target resource. */
    case OPTIONS = "OPTIONS";

    /** @var string TRACE method performs a message loop-back test along the path to the target resource. */
    case TRACE = "TRACE";

    /** @var string PATCH method applies partial modifications to a resource. */
    case PATCH = "PATCH";

    /** @var string ALL methods. */
    case ALL = "ALL";

    /** @var array List of enum names. */
    public const NAMES = array("GET", "HEAD", "POST", "PUT", "DELETE", "CONNECT", "OPTIONS", "TRACE", "PATCH", "ALL");

    /** @var array List of enum values. */
    public const VALUES = array("GET", "HEAD", "POST", "PUT", "DELETE", "CONNECT", "OPTIONS", "TRACE", "PATCH", "ALL");

    /**
     * Get static enum from name
     * 
     * @param value-of<self::NAMES>|'GET'|'HEAD'|'POST'|'PUT'|'DELETE'|'CONNECT'|'OPTIONS'|'TRACE'|'PATCH'|'ALL' $name The enum name to be returned
     * @return self
     */
    public static function fromName(string $name): self {
        return match($name) {
            "GET" => self::GET,
            "HEAD" => self::HEAD,
            "POST" => self::POST,
            "PUT" => self::PUT,
            "DELETE" => self::DELETE,
            "CONNECT" => self::CONNECT,
            "OPTIONS" => self::OPTIONS,
            "TRACE" => self::TRACE,
            "PATCH" => self::PATCH
        };
    }

    /**
     * Get static enum from value
     * 
     * @param value-of<self::VALUES> $value The enum value to be returned
     * @return self
     */
    public static function fromValue(string $value): self {
        return self::from($value);
    }

    /**
     * Get enum value from instance
     *
     * @return value-of<self::VALUES>
     */
    public function getValue(): string {
        return match($this) {
            self::GET => "GET",
            self::HEAD => "HEAD",
            self::POST => "POST",
            self::PUT => "PUT",
            self::DELETE => "DELETE",
            self::CONNECT => "CONNECT",
            self::OPTIONS => "OPTIONS",
            self::TRACE => "TRACE",
            self::PATCH => "PATCH",
            self::ALL => "ALL"
        };
    }

    /**
     * Get enum name from instance
     *
     * @return value-of<self::NAMES>
     */
    public function getName(): string {
        return match($this) {
            self::GET => "GET",              
            self::HEAD => "HEAD",
            self::POST => "POST",
            self::PUT => "PUT",
            self::DELETE => "DELETE",
            self::CONNECT => "CONNECT",
            self::OPTIONS => "OPTIONS",
            self::TRACE => "TRACE",
            self::PATCH => "PATCH",
            self::ALL => "ALL"
        };
    }
}