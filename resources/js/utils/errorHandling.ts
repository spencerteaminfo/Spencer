function ErrorHandlingMessage(id:string, text:string, classD :string = ""):HTMLDivElement{
    const errorDivMsg = document.createElement("div");
    const errorSmallText = document.createElement("small");
    errorDivMsg.id=id;
    errorDivMsg.className = `text-danger ${classD}`;
    errorSmallText.textContent=text;
    errorDivMsg.appendChild(errorSmallText);
    return errorDivMsg;
}

function ErrorHandlingForm(idInput:any, idDiv:string, text:string, classD:string =""){
    idInput?.classList.add("border", "border-danger", "text-danger");
    const errorDivMessage = ErrorHandlingMessage(idDiv, text, classD);
    idInput.insertAdjacentElement("afterend", errorDivMessage);
    idInput.addEventListener("input", () => {
        idInput?.classList.remove("border", "border-danger", "text-danger");
        errorDivMessage?.remove();
    })
}
export default ErrorHandlingForm;