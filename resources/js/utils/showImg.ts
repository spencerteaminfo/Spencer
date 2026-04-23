const isValidImage = (file:File):Promise<boolean> => { 
    return new Promise(resolve => {
        const img = new Image();
        const url = URL.createObjectURL(file);

        img.onload = () => {
            URL.revokeObjectURL(url);
            resolve(true);
        }
        img.onerror = () =>{
            URL.revokeObjectURL(url);
            resolve(false);  
        } 

        img.src = url;
});
}

const setupImageInput = () => {
    let urlImage:string | null = null
    const input = document.getElementById("event-image-upload") as HTMLInputElement;
    if (!input) {
        console.error("Input nebyl na strance nalezen");
        return;
    }
    input.addEventListener("change", async (event:Event) =>{
        const target = event.target as HTMLInputElement;
        const file = target.files?.[0];
        if (urlImage) {
            URL.revokeObjectURL(urlImage)
            urlImage = null;
        }
        if (file) {
            const isImgValid = await isValidImage(file);
            if (isImgValid) {
                const imgPreview = document.getElementById("img-preview") as HTMLImageElement;
                if (imgPreview) {
                    const uploadImg= URL.createObjectURL(file);
                    urlImage = uploadImg; //  URL object only makes 1 img at the same to due to UX
                    imgPreview.src = uploadImg;
                    imgPreview.classList.remove("d-none");   
                    if (!document.getElementById("remove-thumbnail-btn")) {
                        const createDivTextRemoveThumbnailMessageContainer = document.createElement("div") as HTMLDivElement;
                        const createRemoveThumbnailMessage = document.createElement("span") as HTMLSpanElement;

                        createDivTextRemoveThumbnailMessageContainer.id="remove-thumbnail-text-container";
                        createDivTextRemoveThumbnailMessageContainer.classList.add("d-flex", "w-100", "justify-content-end");

                        createRemoveThumbnailMessage.classList.add ("mt-2", "fs-6", "text-danger", "mb-3", "cursor-pointer", "hover-underline"); 
                        createRemoveThumbnailMessage.id = "remove-thumbnail-btn";
                        createRemoveThumbnailMessage.textContent = "Remove Thubnail";
                        createRemoveThumbnailMessage.addEventListener("click", () =>{
                            imgPreview.classList.add("d-none");
                            createRemoveThumbnailMessage?.remove();
                            imgPreview.src = "";
                            URL.revokeObjectURL(uploadImg);
                            target.value="";
                        })
                        createDivTextRemoveThumbnailMessageContainer.append(createRemoveThumbnailMessage);
                        const previewDiv = document.querySelector("#img-preview-div") as HTMLDivElement;
                        if (previewDiv) {
                            previewDiv.insertAdjacentElement("afterend", createDivTextRemoveThumbnailMessageContainer);
                        }
                        
                    }

                }
            } else{
                console.error("chyba blby dormat");
            }
        } else{
            console.error("fiel neni file");
        }
    })
}
setupImageInput();