import { ElMessage, ElMessageBox } from 'element-plus'

function confirm({title='提示',message='',cancelButtonText='取消',confirmButtonText='确定'}){
	return new Promise((resolve,reject)=>{
        ElMessageBox.confirm(message,title,{
            cancelButtonText: cancelButtonText,
            confirmButtonText: confirmButtonText,
            center: true
		}).then(() => {
			resolve()
		}).catch(() => {
			reject()
		});
	})
}

function alert({title='',message='提示',cancelButtonText='取消',confirmButtonText='确定'}){
	return new Promise((resolve,reject)=>{
        ElMessageBox.alert(message,title,{
            confirmButtonText: confirmButtonText,
            center: true
        }).then(() => {
            resolve()
        }).catch(() => {
            reject()
        });
	})
}

export default {
	confirm,
	alert
}
