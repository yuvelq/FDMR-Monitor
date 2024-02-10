from logging import getLogger
from pathlib import Path
from logging.config import dictConfig

__author__ = 'Christian Quiroz, OA4DOA'
__verion__ = '1.0.0'
__copyright__ = 'Copyright (c) 2023 Christian Quiroz, OA4DOA'
__license__ = 'GNU GPLv3'
__maintainer__ = 'Christian Quiroz, OA4DOA'
__email__ = 'adm@dmr-peru.net'

def create_logger(conf):
    dictConfig({
        'version': 1,
        'disable_existing_loggers': False,
        'formatters': {
            'std_format': {
                'format': '%(asctime)s %(levelname)s %(message)s',
                'datefmt' : '%Y-%m-%d %H:%M:%S'
            }
        },
        'handlers': {
            'console': {
                'class': 'logging.StreamHandler',
                'formatter': 'std_format',
                'level': conf['LOG_LEVEL']
            },
            'file': {
                'class': 'logging.FileHandler',
                'formatter': 'std_format',
                'filename': Path(conf['PATH'], conf['LOG_FILE']),
                'level': conf['LOG_LEVEL']
            }
        },
        'root': {
            'handlers': conf['LOG_HANDLERS'],
            'level': 'NOTSET',
        }
    })

    return getLogger(__name__)


if __name__ == "__main__":
    log_conf = {
        'PATH': './',
        'LOG_FILE': 'test.log',
        'LOG_LEVEL': 'DEBUG',
        'LOG_HANDLERS': [
            'console',
            'file'
        ]
    }

    logger = create_logger(log_conf)
    
    logger.debug('This is debug')
    logger.info('test')
    logger.warning('test1')
    logger.error("This's an error")
