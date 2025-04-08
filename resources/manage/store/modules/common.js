import { defineStore } from 'pinia'
export const useCommonStore = defineStore('shop-common', {
    state: () => {
        return {
            shopConfig:{},
            adminUser:{},
            visitedViews: [],
            cachedViews: [],
            refreshView:{}
        }
    },
    actions: {
        updateShopConfig(value){
            this.shopConfig = value
        },
        updateAdminUser(value){
            this.adminUser = value
        },
        addVisitedViews(view) {
            if (this.visitedViews.some(item => item.path === view.path&&JSON.stringify(item.query) === JSON.stringify(view.query))) return
            let samePathIndex = -1

            this.visitedViews.length&&this.visitedViews.forEach((item,i)=>{
                if(item.path === view.path){
                    samePathIndex = i
                }
            })
            if(samePathIndex>-1){
                this.visitedViews[samePathIndex].query = view.query
            }else {
                this.visitedViews.push({
                    name: view.name,
                    path: view.path,
                    title: view.meta.title || 'no-name',
                    query: view.query,
                })
            }

            /** 添加页面keep-alive缓存 **/
            if (view.meta && view.meta.keepAlive) {
                this.cachedViews.push(view.name)
            }
            this.cachedViews = [...new Set(this.cachedViews)]
        },
        delVisitedViews(view) {
            return new Promise((resolve) => {
                let path = view.path ? view.path : ''
                this.visitedViews.forEach((item, index) => {
                    if (item.path === path) {
                        this.visitedViews.splice(index, 1)
                        /** 删除页面keep-alive缓存 **/
                        if (view.meta && view.meta.keepAlive) {
                            this.cachedViews.length && this.cachedViews.splice(index, 1)
                        }
                    }
                })
                resolve([...this.visitedViews])
            })
        },
        delOthersViews(view) {
            return new Promise((resolve) => {
                for (const [i, v] of this.visitedViews.entries()) {
                    if (v.path === view.path) {
                        this.visitedViews = this.visitedViews.slice(i, i + 1)
                        /** 删除页面keep-alive缓存 **/
                        if (view.meta && view.meta.keepAlive) {
                            this.cachedViews.length && (this.cachedViews = this.cachedViews.slice(i, i + 1))
                        }
                        break
                    }
                }
                resolve([...this.visitedViews])
            })
        },
        delAllViews(state) {
            return new Promise((resolve) => {
                this.visitedViews = []
                this.cachedViews = []
                resolve([...this.visitedViews])
            })
        },
        addCachedViews(view) {
            return new Promise((resolve) => {
                /** 添加页面keep-alive缓存 **/
                if (view.meta && view.meta.keepAlive) {
                    this.cachedViews.push(view.name)
                }
                this.cachedViews = [...new Set(this.cachedViews)]
                resolve([...this.cachedViews])
            })
        },
        refreshQuery(view){
            return new Promise((resolve) => {
                this.refreshView = view
                resolve()
            })
        }
    }
})

