#!/usr/bin/env python3
"""Seed 50 active services into an empty, migrated SQLite benchmark database."""

import argparse
import sqlite3
from datetime import datetime, timezone
from pathlib import Path
from urllib.parse import quote


def main() -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("database", type=Path, help="path to an empty, migrated SQLite database")
    args = parser.parse_args()

    database = args.database.expanduser().resolve()
    if not database.is_file():
        parser.error(f"database does not exist: {database}")

    connection = sqlite3.connect(f"file:{quote(str(database), safe='/')}?mode=rw", uri=True)
    try:
        table = connection.execute(
            "SELECT name FROM sqlite_master WHERE type = 'table' AND name = 'services'"
        ).fetchone()
        if table is None:
            parser.error("database is missing the migrated services table")

        count = connection.execute("SELECT COUNT(*) FROM services").fetchone()[0]
        if count:
            parser.error("services table must be empty to avoid duplicating benchmark data")

        created_at = datetime.now(timezone.utc).isoformat(sep=" ", timespec="seconds")
        connection.executemany(
            """
            INSERT INTO services (name, description, is_active, created_at, updated_at)
            VALUES (?, ?, 1, ?, ?)
            """,
            [
                (
                    f"Demonstration service {number:02}",
                    "Service used for the local performance measurement.",
                    created_at,
                    created_at,
                )
                for number in range(1, 51)
            ],
        )
        connection.commit()
        print(f"Inserted 50 active services into {database}")
    finally:
        connection.close()

    return 0


if __name__ == "__main__":
    raise SystemExit(main())
