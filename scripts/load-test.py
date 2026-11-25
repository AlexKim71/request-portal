#!/usr/bin/env python3
"""Run a small, dependency-free HTTP load test against the local application."""

import argparse
from http.client import HTTPException
import math
import sys
import time
from concurrent.futures import ThreadPoolExecutor
from urllib.error import HTTPError, URLError
from urllib.request import Request, urlopen


def fetch(url: str, timeout: float) -> tuple[bool, float, int | None]:
    started = time.perf_counter()
    status = None

    try:
        request = Request(url, headers={"User-Agent": "request-portal-load-test/1.0"})
        with urlopen(request, timeout=timeout) as response:
            status = response.status
            response.read()
            success = 200 <= status < 300
    except HTTPError as error:
        status = error.code
        with error:
            error.read()
        success = False
    except (HTTPException, URLError, TimeoutError, OSError):
        success = False

    return success, (time.perf_counter() - started) * 1000, status


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--url", default="http://127.0.0.1:8000/services")
    parser.add_argument("--requests", type=int, default=200, dest="request_count")
    parser.add_argument("--concurrency", type=int, default=10)
    parser.add_argument("--timeout", type=float, default=5)
    parser.add_argument("--max-p95-ms", type=float, default=1000)
    args = parser.parse_args()

    if args.request_count < 1 or args.concurrency < 1 or args.timeout <= 0 or args.max_p95_ms <= 0:
        parser.error("requests, concurrency, timeout, and max-p95-ms must be positive")

    started = time.perf_counter()
    with ThreadPoolExecutor(max_workers=args.concurrency) as executor:
        results = list(executor.map(
            lambda _: fetch(args.url, args.timeout),
            range(args.request_count),
        ))
    elapsed = time.perf_counter() - started

    successful = sum(success for success, _, _ in results)
    latencies = sorted(latency for _, latency, _ in results)
    p95 = latencies[math.ceil(0.95 * len(latencies)) - 1]
    availability = successful / args.request_count * 100
    throughput = args.request_count / elapsed
    statuses = sorted({status for _, _, status in results if status is not None})

    print(f"URL: {args.url}")
    print(f"Requests: {args.request_count}; concurrency: {args.concurrency}")
    print(f"Successful responses: {successful}/{args.request_count} ({availability:.2f}%)")
    print(f"HTTP statuses: {', '.join(map(str, statuses)) or 'none'}")
    print(f"Elapsed: {elapsed:.3f} s; throughput: {throughput:.2f} requests/s")
    print(f"Latency: p50 {latencies[math.ceil(0.50 * len(latencies)) - 1]:.2f} ms; "
          f"p95 {p95:.2f} ms; max {latencies[-1]:.2f} ms")
    print(f"Acceptance (all responses successful and p95 <= {args.max_p95_ms:.0f} ms): "
          f"{'PASS' if successful == args.request_count and p95 <= args.max_p95_ms else 'FAIL'}")

    return 0 if successful == args.request_count and p95 <= args.max_p95_ms else 1


if __name__ == "__main__":
    sys.exit(main())
