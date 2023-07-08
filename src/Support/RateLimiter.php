<?php

namespace AresInspired\HyperfExceptionNotify\Support;

use Closure;
use Hyperf\Redis\Redis;
use Hyperf\Support\Traits\InteractsWithTime;

class RateLimiter {

	use InteractsWithTime;

	/**
	 * The configured limit object resolvers.
	 *
	 * @var array
	 */
	protected array $limiters = [];

	/**
	 * Create a new rate limiter instance.
	 *
	 * @param \Hyperf\Redis\Redis $redis
	 */
	public function __construct(protected Redis $redis) {

	}

	/**
	 * Register a named limiter configuration.
	 *
	 * @param string   $name
	 * @param \Closure $callback
	 * @return $this
	 */
	public function for(string $name, Closure $callback): static {
		$this->limiters[$name] = $callback;

		return $this;
	}

	/**
	 * Get the given named rate limiter.
	 *
	 * @param string $name
	 * @return \Closure|null
	 */
	public function limiter(string $name): ?Closure {
		return $this->limiters[$name] ?? null;
	}

	/**
	 * Attempts to execute a callback if it's not limited.
	 *
	 * @param string   $key
	 * @param int      $maxAttempts
	 * @param \Closure $callback
	 * @param int      $decaySeconds
	 * @return mixed
	 */
	public function attempt(string $key, int $maxAttempts, Closure $callback, int $decaySeconds = 60): mixed {
		if ($this->tooManyAttempts($key, $maxAttempts)) {
			return false;
		}

		return \Hyperf\Tappable\tap($callback() ?: true, function () use ($key, $decaySeconds) {
			$this->hit($key, $decaySeconds);
		});
	}

	/**
	 * Determine if the given key has been "accessed" too many times.
	 *
	 * @param string $key
	 * @param int    $maxAttempts
	 *
	 * @return bool
	 */
	public function tooManyAttempts(string $key, int $maxAttempts): bool {
		if ($this->attempts($key) >= $maxAttempts) {
			if ($this->redis->get($this->cleanRateLimiterKey($key) . ':timer')) {
				return true;
			}

			$this->resetAttempts($key);
		}

		return false;
	}

	/**
	 * Increment the counter for a given key for a given decay time.
	 *
	 * @param string $key
	 * @param int    $decaySeconds
	 * @return int
	 */
	public function hit(string $key, int $decaySeconds = 60): int {
		$key = $this->cleanRateLimiterKey($key);

		$this->redis->set(
			$key . ':timer', $this->availableAt($decaySeconds), $decaySeconds
		);

		$hits = $this->redis->incr($key);
		$hits === 1 and $this->redis->expire($key, $decaySeconds);

		return $hits;
	}

	/**
	 * Get the number of attempts for the given key.
	 *
	 * @param string $key
	 * @return int
	 */
	public function attempts(string $key): int {
		$key = $this->cleanRateLimiterKey($key);

		return $this->redis->get($key) ?? 0;
	}

	/**
	 * Reset the number of attempts for the given key.
	 *
	 * @param string $key
	 * @return int
	 */
	public function resetAttempts(string $key): int {
		$key = $this->cleanRateLimiterKey($key);

		return $this->redis->del($key);
	}

	/**
	 * Get the number of retries left for the given key.
	 *
	 * @param string $key
	 * @param int    $maxAttempts
	 * @return int
	 */
	public function remaining(string $key, int $maxAttempts): int {
		$key = $this->cleanRateLimiterKey($key);

		$attempts = $this->attempts($key);

		return $maxAttempts - $attempts;
	}

	/**
	 * Get the number of retries left for the given key.
	 *
	 * @param string $key
	 * @param int    $maxAttempts
	 * @return int
	 */
	public function retriesLeft(string $key, int $maxAttempts): int {
		return $this->remaining($key, $maxAttempts);
	}

	/**
	 * Clear the hits and lockout timer for the given key.
	 *
	 * @param string $key
	 * @return void
	 */
	public function clear(string $key) {
		$key = $this->cleanRateLimiterKey($key);

		$this->resetAttempts($key);

		$this->redis->del($key . ':timer');
	}

	/**
	 * Get the number of seconds until the "key" is accessible again.
	 *
	 * @param string $key
	 * @return int
	 */
	public function availableIn(string $key): int {
		$key = $this->cleanRateLimiterKey($key);

		return max(0, $this->redis->get($key . ':timer') - $this->currentTime());
	}

	/**
	 * Clean the rate limiter key from unicode characters.
	 *
	 * @param string $key
	 * @return string
	 */
	public function cleanRateLimiterKey(string $key): string {
		return preg_replace('/&([a-z])[a-z]+;/i', '$1', htmlentities($key));
	}
}