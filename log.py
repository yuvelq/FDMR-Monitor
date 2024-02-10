import sys

from twisted.logger import Logger, LogLevelFilterPredicate, LogLevel, FilteringLogObserver, textFileLogObserver, globalLogPublisher

def get_log_level(level):
    level = level.lower()
    if level == 'info':
        _level = LogLevel.info
    elif level == 'debug':
        _level = LogLevel.debug
    elif level == 'warn':
        _level = LogLevel.warn
    elif level == 'error':
        _level = LogLevel.error
    elif level == 'critical':
        _level = LogLevel.critical
    else:
        _level = LogLevel.warn
    return _level


def create_logger(conf):
    level = get_log_level(conf['LOG_LEVEL'])

    log_level = LogLevelFilterPredicate(level)
    time_format = '%b %m %H:%M:%S.%f'

    logger = Logger()

    handlers = conf['LOG_HANDLERS'].replace(' ', '').lower().split(',')
    if 'console' in handlers:
        console = FilteringLogObserver(textFileLogObserver(sys.stdout, time_format), [log_level])
        globalLogPublisher.addObserver(console)

    if 'file' in handlers:
        file = FilteringLogObserver(textFileLogObserver(open(conf['LOG_FILE'], 'a'), time_format), [log_level])
        globalLogPublisher.addObserver(file)

    return (logger, log_level)
