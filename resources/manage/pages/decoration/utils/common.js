/**
 * 根据传入的键路径数组来更新多级对象中的某个值
 * @params: {
 *      obj: {}, 原数据对象
 *      keys: ['a', 'b', 'c'], 键路径数组
 *      newValue: '', 新值
 *      createIfMissing: false, 如果键路径不存在，是否创建新键路径
 * }
 */
export const updateNested = (obj, keys, newValue, createIfMissing = false) => {
    // 检查对象是否为 null 或 undefined
    if (!obj || typeof obj !== 'object') {
        return obj;
    }
  
    // 检查键路径数组是否为空
    if (!Array.isArray(keys) || keys.length === 0) {
        return obj;
    }
  
    let current = obj;
    const lastKeyIndex = keys.length - 1;
  
    for (let i = 0; i < keys.length; i++) {
        const key = keys[i];
  
        // 如果是最后一个键，直接赋值
        if (i === lastKeyIndex) {
            current[key] = newValue;
        } else {
            // 如果当前键不存在，且允许创建，则创建一个新的对象
            if (current[key] === undefined && createIfMissing) {
                current[key] = {};
            }
            // 如果当前键不存在且不允许创建，直接返回
            if (current[key] === undefined) {
                return obj;
            }
            // 进入下一层级
            current = current[key];
        }
    }
  
    return obj;
}