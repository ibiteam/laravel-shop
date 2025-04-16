import router from './index'
import { useCommonStore } from '@/store'

/**
 * 关闭指定名称的标签页（tab）。
 *
 * 当指定的标签页被关闭后：
 * - 如果 `openRoute` 不为空，将跳转到指定的路由；
 * - 否则，将自动跳转到最近访问的标签页；
 * - 如果没有其他标签页，则跳转到首页 `manage.home.index`。
 *
 * @function tabRemove
 * @param {string} name - 要关闭的标签页的路由名称。
 * @param {Object|null} openRoute - （可选）关闭后跳转的路由对象，格式为：
 *   {
 *     name: string,               // 跳转的路由名
 *     query?: Record<string, any>, // 可选的 query 参数
 *     params?: Record<string, any> // 可选的 params 参数
 *   }
 *
 * @example
 * // 关闭当前 tab，并跳转到商品列表页
 * tabRemove('manage.goods.edit', { name: 'manage.goods.index' });
 *
 */
export function tabRemove(name: string, openRoute: any = null) {
    const commonStore = useCommonStore()
    const view = commonStore.visitedViews.find(item => item.name === name);

    if (!view) return; // 防止找不到 view 时出错

    // 如果是首页，且只剩一个 tab，就不允许关闭
    if (name === 'manage.home.index' && commonStore.visitedViews.length === 1) return;

    commonStore.delVisitedViews(view).then((views) => {
        if (!openRoute) {
            const latestView = views.at(-1); // ES2022 简写，等价于 views[views.length - 1]
            router.push(
                latestView
                    ? { name: latestView.name, query: latestView.query ?? {}, params: latestView.params ?? {} }
                    : { name: 'manage.home.index' }
            );
        } else {
            router.push({
                name: openRoute.name,
                query: openRoute.query ?? {},
                params: openRoute.params ?? {}
            });
        }
    });
}
