# Camelot Dockerized

> [!WARNING]
> This project is in its early stages and may not be fully functional. Use at your own risk.

This repository provides a Dockerized setup for processing PDF files and extracting table data using [Camelot](https://camelot-py.readthedocs.io/).

## Features

- **PDF Table Extraction**: Extract tables from PDF files using Camelot.
- **Dockerized Environment**: Easily deployable with Docker.
- **REST API**: Upload and process PDF files via a simple API.
- **Composer Integration**: PHP dependencies managed with Composer.

## Prerequisites

- Docker and Docker Compose.

## Setup

1. Clone the repository:
    ```bash
    git clone https://github.com/your-repo/Camelot-dockerized.git
    cd Camelot-dockerized
    ```

2. Build and start the Docker containers:
    ```bash
    docker-compose up --build
    ```

3. Access the application at `http://localhost:8080`.

## API Usage

### Upload PDF and Extract Tables

- **Endpoint**: `POST /`
- **Request**: Upload a PDF file as `pdf_file` in a `multipart/form-data` request.
- **Response**: JSON response with extracted table data.

## Environment Variables

- `UPLOAD_MAX_FILESIZE`: Maximum upload file size (default: `100M`).
- `POST_MAX_SIZE`: Maximum POST request size (default: `100M`).