FROM golang:alpine

WORKDIR /app/

COPY ./migrations /app/migrations
COPY ./.env /app/

RUN apk add --no-cache git make bash build-base

RUN git clone https://github.com/vishnubob/wait-for-it.git
RUN go env -w GO111MODULE=on
RUN go env -w CGO_ENABLED=1
RUN go env -w GOPROXY="https://goproxy.io,direct"
RUN go env -w GOPRIVATE=github.com/mattn/go-sqlite3
RUN go get -v github.com/rubenv/sql-migrate/...
