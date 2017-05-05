# TODO

## Commands

1. Initial implementation. Command is there, but may or may not work.
2. Command has initial test coverage.
3. Command has test coverage that makes sense, including a connected (functional) test
4. Command implementation is considered finished

Command  | Class               | 1 | 2 | 3 | 4
-------- | -----------------   | -------- | -------- | -------- | -------- |
ACKJOB   | `JobAck`            | &#10004; | &#10004; |          |          |
ADDJOB   | `JobAdd`            | &#10004; | &#10004; | &#10004; |          |
DELJOB   | `JobDelete`         | &#10004; | &#10004; | &#10004; |          |
DEQUEUE  | `JobDequeue`        | &#10004; | &#10004; |          |          |
ENQUEUE  | `JobEnqueue`        | &#10004; | &#10004; |          |          |
FASTACK  | `JobFastAck`        | &#10004; | &#10004; |          |          |
GETJOB   | `JobGet`            | &#10004; | &#10004; | &#10004; |          |
SHOW     | `JobShow`           | &#10004; | &#10004; |          |          |
WORKING  | `JobWorking`        | &#10004; |          |          |          |
JSCAN    | `JobScan`           | &#10004; | &#10004; |          |          |
NACK     | `JobNack`           | &#10004; |          |          |          |
PAUSE    | `QueuePause`        | &#10004; |          |          |          |
QLEN     | `QueueLength`       | &#10004; | &#10004; | &#10004; |          |
QPEEK    | `QueuePeek`         | &#10004; | &#10004; |          |          |
QSCAN    | `QueueScan`         | &#10004; | &#10004; |          |          |
QSTAT    | `QueueStatistics`   | &#10004; |          |          |          |
CLUSTER  | `ServerCluster`     | &#10004; | &#10004; |          |          |
CONFIG   | `ServerConfig`      | &#10004; |          |          |          |
DEBUG    | `ServerDebug`       | &#10004; |          |          |          |
HELLO    | `ServerHello`       | &#10004; |          |          |          |
INFO     | `ServerInfo`        | &#10004; | &#10004; |          |          |
