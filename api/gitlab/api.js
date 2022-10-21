import { createBlob } from '../../modules/blob.js'
import { createLink } from '../../modules/link.js'
import { B, get, getAll, getByClass } from '../../modules/dom.js'
import { fileExt } from '../../modules/FS.js'
import { getImgData } from '../../modules/img.js'

//await import('https://cdn/api/gitlab/api.js')

//(await import('https://cdn/api/gitlab/api.js')).init()
export const init = async () => {
	const author = getByClass('author')[0].innerHTML
	const id = B.dataset.pageTypeId
	const file = `data/gitlab/${author}/${id}.html`
	const header = get('.detail-page-header-body')

	const title = get('title').outerHTML
	const style = get('style').outerHTML
	const issue = getByClass('issue-details')[0]
	const svgList = getAll('svg', issue)
	const imgList = getAll('img', issue)
	let imgUrlList = []
	svgList.forEach((svg) => svg.remove())

	const getLink = () => {
		const issueHtml = issue.outerHTML
		const html = `<!DOCTYPE html>
			<html class="" lang="en">
			<head prefix="og: http://ogp.me/ns#">
				${title}
				${style}
			</head>
			<body class="gl-dark tab-width-8 gl-browser-chrome gl-platform-windows" data-find-file="/qastart/web1-group/frontend/-/find_file/main" data-group="web1-group" data-namespace-id="57502874" data-page="projects:issues:show" data-page-type-id="89" data-project="frontend" data-project-id="39569241">
				<div class="layout-page hide-when-top-nav-responsive-open page-gutter right-sidebar-collapsed page-with-contextual-sidebar">
					<div class="content-wrapper content-wrapper-margin">
						<div class="container-fluid container-limited limit-container-width project-highlight-puc">
							${issueHtml}
						</div>
					</div>
				</div>
			</body>
		</html>`
		createLink(html, file, 'html', '.detail-page-header-body')
	}

	console.log(title)
	console.log(style)
	console.log(issue)

	console.log(author)
	console.log(id)
	console.log(file)
	console.log(header)

	console.log(imgList)

	imgList.forEach((img) => {
		console.log(img)
		console.log(img.src)
		const ext = fileExt(img.src)
		console.log(ext)
		const { base64image } = getImgData(img, ext)
		img.src = base64image
		imgUrlList.push(base64image)
		if (imgUrlList.length == imgList.length) getLink()
	})
}
