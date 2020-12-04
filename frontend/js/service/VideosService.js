class VideosService {
    constructor() {
    }

    uploadvideo(formData){
        return $.ajax({
            url: AppConfig.backendServer+'/rest/video',
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false
        });
    }

    dislike(id){
        return $.ajax({
            url: AppConfig.backendServer +'/rest/video/like/' + id,
            method: 'DELETE'
        });
    }

    like(id){
        return $.ajax({
            url: AppConfig.backendServer+'/rest/video/like/' + id,
            method: 'POST'
        });
    }

    getVideo(id){
        return $.get(AppConfig.backendServer+'/rest/video/'+ id);
    }

    deleteVideo(id){
        return $.ajax({
            url: AppConfig.backendServer +'/rest/video/' + id,
            method: 'DELETE'
        });
    }

    search(hashtag, page){
        return $.get(AppConfig.backendServer+'/rest/video/hashtag/'+ hashtag + '/' + page);
    }
}