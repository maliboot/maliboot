<?php

declare(strict_types=1);

namespace MaliBoot\Cola\Infra;

/**
 * 数据操作连接符.
 */
enum QueryConnector: string
{
    /*
     * 升序.
     */
    case ORDER_BY_ASC = 'ASC';

    /*
     * 降序.
     */
    case ORDER_BY_DESC = 'DESC';

    /*
     * 等于.
     */
    case OPERATOR_EQ = '=';

    /*
     * 不等于.
     */
    case OPERATOR_NE = '!=';

    /*
     * 小于.
     */
    case OPERATOR_LT = '<';

    /*
     * 小于等于.
     */
    case OPERATOR_LTE = '<=';

    /*
     * 大于.
     */
    case OPERATOR_GT = '>';

    /*
     * 大于等于.
     */
    case OPERATOR_GTE = '>=';

    /*
     * 左括号.
     */
    case BRACKET_OPEN = '(';

    /*
     * 右括号.
     */
    case BRACKET_CLOSE = ')';

    /*
     * 修饰符in.
     */
    case IN = 'IN';

    /*
     * 修饰符not in.
     */
    case NOT_IN = 'NOT IN';

    /*
     * 修饰符like.
     */
    case LIKE = 'LIKE';

    /*
     * 修饰符in.
     */
    case NOT_LIKE = 'NOT LIKE';

    /*
     * 修饰符between.
     */
    case BETWEEN = 'BETWEEN';

    /*
     * 修饰符not between.
     */
    case NOT_BETWEEN = 'NOT BETWEEN';

    /*
     * 内连接.
     */
    case INNER_JOIN = 'INNER JOIN';

    /*
     * 左连接.
     */
    case LEFT_JOIN = 'LEFT JOIN';

    /*
     * 右连接.
     */
    case RIGHT_JOIN = 'RIGHT JOIN';

    /*
     * 逻辑运算符and.
     */
    case LOGICAL_AND = 'AND';

    /*
     * 逻辑运算符or.
     */
    case LOGICAL_OR = 'OR';

    /*
     * is判断语句.
     */
    case IS = 'IS';

    /*
     * is not 判断语句.
     */
    case IS_NOT = 'IS NOT';
    case DATE = 'DATE';
    case DAY = 'DAY';
    case MONTH = 'MONTH';
    case YEAR = 'YEAR';
    case EXISTS = 'EXISTS';
    case HAS = 'HAS';
    case HAS_MORPH = 'HAS MORPH';
    case DOESNT_HAVE = 'DOESNT HAVE';
    case DOESNT_HAVE_MORPH = 'DOESNT HAVE MORPH';
    case BETWEEN_COLUMNS = 'BETWEEN COLUMNS';
    case NOT_BETWEEN_COLUMNS = 'NOT BETWEEN COLUMNS';
    case RAW = 'RAW';
}
