import { defineStore } from 'pinia'
export const useCommonStore = defineStore('shop-common', {
    state: () => {
        return {
            shopConfig:{},
            adminUser:{},
            visitedViews: [],
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
        },
        delVisitedViews(view) {
            return new Promise((resolve) => {
                let path = view.path ? view.path : ''
                this.visitedViews.forEach((item, index) => {
                    if (item.path === path) {
                        this.visitedViews.splice(index, 1)
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
                        break
                    }
                }
                resolve([...this.visitedViews])
            })
        },
        delAllViews() {
            return new Promise((resolve) => {
                this.visitedViews = []
                resolve([...this.visitedViews])
            })
        },
        refreshQuery(view){
            return new Promise((resolve) => {
                this.refreshView = view
                resolve()
            })
        },
        updateVisitedViewsTitle(view,title){
            this.visitedViews.forEach(item => {
                if (item.name == view.name){
                    item.title = title
                }
            })
        },
        resetVisitedViews(){
            this.visitedViews = []
        }
    }
})

