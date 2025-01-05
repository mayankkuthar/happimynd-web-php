/** Reprot  **/

function adjustScale(diagramId, val, zoom) {
    let rotate = 0;
    switch (diagramId) {
        case "selfesteem":
            rotate = val;
            $("#" + diagramId).css(
                "transform",
                "rotate(" + rotate + "deg) scale(" + zoom + ")"
            );
            break;
        case "happiness":
            rotate = val;
            $("#" + diagramId).css(
                "transform",
                "rotate(" + rotate + "deg) scale(" + zoom + ")"
            );
            break;
        case "resilience":
            rotate = val;
            $("#" + diagramId).css(
                "transform",
                "rotate(" + rotate + "deg) scale(" + zoom + ")"
            );
            break;
        case "jobsatisfaction":
            rotate = val;
            $("#" + diagramId).css(
                "transform",
                "rotate(" + rotate + "deg) scale(" + zoom + ")"
            );
            break;
        case "burnout":
            rotate = val;
            $("#" + diagramId).css(
                "transform",
                "rotate(" + rotate + "deg) scale(" + zoom + ")"
            );
            break;
        case "stress":
            rotate = val;
            $("#" + diagramId).css(
                "transform",
                "rotate(" + rotate + "deg) scale(" + zoom + ")"
            );
            break;
        case "anxiety":
            rotate = val;
            $("#" + diagramId).css(
                "transform",
                "rotate(" + rotate + "deg) scale(" + zoom + ")"
            );
            break;
        case "internetaddiction":
            rotate = val;
            $("#" + diagramId).css(
                "transform",
                "rotate(" + rotate + "deg) scale(" + zoom + ")"
            );
            break;
        default:
            rotate = val;
            $("#" + diagramId).css(
                "transform",
                "rotate(" + rotate + "deg) scale(" + zoom + ")"
            );
            break;
    }
}
