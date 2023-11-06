<?php

namespace Framework\Enums;

/**
 * Enum HTTPStatus
 *
 * This enumeration represents different HTTP status codes that a response can have.
 */
enum HTTPStatus: int {
    /** @var int The request has succeeded. */
    case OK = 200;

    /** @var int The request has succeeded and a new resource was created as a result. */
    case CREATED = 201;

    /** @var int There is no content to send for this request, but the headers may be useful. */
    case NO_CONTENT = 204;

    /** @var int The server could not understand the request due to invalid syntax. */
    case BAD_REQUEST = 400;

    /** @var int The client must authenticate itself to get the requested response. */
    case UNAUTHORIZED = 401;

    /** @var int The client does not have access rights to the content; that is, it is unauthorized, so the server is refusing to give the requested resource. */
    case FORBIDDEN = 403;

    /** @var int The server can not find the requested resource. */
    case NOT_FOUND = 404;

    /** @var int The method received in the request-line is known by the origin server but not supported by the target resource. */
    case BAD_METHOD = 405;

    /** @var int This response is sent on an idle connection by some servers, even without any previous request by the client. */
    case TIMEOUT = 408;

    /** @var int Request entity is larger than limits defined by server; the server might close the connection or return an Retry-After header field. */
    case TOO_LARGE = 413;

    /** @var int The media format of the requested data is not supported by the server, so the server is rejecting the request. */
    case BAD_CONTENT = 415;

    /** @var int The user has sent too many requests in a given amount of time ("rate limiting"). */
    case TOO_MANY_REQ = 429;

    /** @var int This status code is used when the connection was closed by client while HTTP server is processing its request, making server unable to send the HTTP header back. */
    case CLIENT_CLOSE_REQ = 499;

    /** @var int The server has encountered a situation it doesn't know how to handle. */
    case INTERNAL_ERROR = 500;

    /** @var array List of enum names. */
    public const NAMES = array("OK", "CREATED", "NO_CONTENT", "BAD_REQUEST", "UNAUTHORIZED", "FORBIDDEN", "NOT_FOUND", "BAD_METHOD", "TIMEOUT", "TOO_LARGE", "BAD_CONTENT", "TOO_MANY_REQ", "CLIENT_CLOSE_REQ", "INTERNAL_ERROR");

    /** @var array List of enum values. */
    public const VALUES = array(200, 201, 204, 400, 401, 403, 404, 405, 408, 413, 415, 429, 499, 500);

    /**
     * Get static enum from name
     * 
     * @param value-of<self::NAMES>|'OK'|'CREATED'|'NO_CONTENT'|'BAD_REQUEST'|'UNAUTHORIZED'|'FORBIDDEN'|'NOT_FOUND'|'BAD_METHOD'|'TIMEOUT'|'TOO_LARGE'|'BAD_CONTENT'|'TOO_MANY_REQ'|'CLIENT_CLOSE_REQ'|'INTERNAL_ERROR' $name The enum name to be returned
     * @return self
     */
    public static function fromName(string $name): self {
        return match($name) {
            "OK" => self::OK,
            "CREATED" => self::CREATED,
            "NO_CONTENT" => self::NO_CONTENT,
            "BAD_REQUEST" => self::BAD_REQUEST,
            "UNAUTHORIZED" => self::UNAUTHORIZED,
            "FORBIDDEN" => self::FORBIDDEN,
            "NOT_FOUND" => self::NOT_FOUND,
            "BAD_METHOD" => self::BAD_METHOD,
            "TIMEOUT" => self::TIMEOUT,
            "TOO_LARGE" => self::TOO_LARGE,
            "BAD_CONTENT" => self::BAD_CONTENT,
            "TOO_MANY_REQ" => self::TOO_MANY_REQ,
            "CLIENT_CLOSE_REQ" => self::CLIENT_CLOSE_REQ,
            "INTERNAL_ERROR" => self::INTERNAL_ERROR
        };
    }

    /**
     * Get static enum from value
     * 
     * @param value-of<self::VALUES>|int $value The enum value to be returned
     * @return self
     */
    public static function fromValue(int $value): self {
        return self::from($value);
    }

    /**
     * Get enum value from instance
     *
     * @return value-of<self::VALUES>
     */
    public function getValue(): int {
        return match($this) {
            self::OK => 200,
            self::CREATED => 201,
            self::NO_CONTENT => 204,
            self::BAD_REQUEST => 400,
            self::UNAUTHORIZED => 401,
            self::FORBIDDEN => 403,
            self::NOT_FOUND => 404,
            self::BAD_METHOD => 405,
            self::TIMEOUT => 408,
            self::TOO_LARGE => 413,
            self::BAD_CONTENT => 415,
            self::TOO_MANY_REQ => 429,
            self::CLIENT_CLOSE_REQ => 499,
            self::INTERNAL_ERROR => 500
        };
    }

    /**
     * Get enum name from instance
     *
     * @return value-of<self::NAMES>
     */
    public function getName(): string {
        return match($this) {
            self::OK => "OK",              
            self::CREATED => "CREATED",
            self::NO_CONTENT => "NO_CONTENT",
            self::BAD_REQUEST => "BAD_REQUEST",
            self::UNAUTHORIZED => "UNAUTHORIZED",
            self::FORBIDDEN => "FORBIDDEN",
            self::NOT_FOUND => "NOT_FOUND",
            self::BAD_METHOD => "BAD_METHOD",
            self::TIMEOUT => "TIMEOUT",
            self::TOO_LARGE => "TOO_LARGE",
            self::BAD_CONTENT => "BAD_CONTENT",
            self::TOO_MANY_REQ => "TOO_MANY_REQ",
            self::CLIENT_CLOSE_REQ => "CLIENT_CLOSE_REQ",
            self::INTERNAL_ERROR => "INTERNAL_ERROR"
        };
    }
}