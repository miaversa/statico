package main

import (
	"io/ioutil"
	"os"
	"strings"

	stcss "github.com/miaversa/statico/pkg/css"
	sthtml "github.com/miaversa/statico/pkg/html"
)

func aboutPage() {
	source := "source/sobre/index.html"
	dir := "public/sobre/"
	filename := dir + "index.html"

	f, err := os.Open(source)
	if err != nil {
		panic(err)
	}
	defer f.Close()

	styles := stcss.Derive(f)

	bytes, err := ioutil.ReadFile(source)
	if err != nil {
		panic(err)
	}

	html := string(bytes)
	html = strings.Replace(html, "/* style */", styles, -1)
	html = sthtml.Minify(html)

	os.MkdirAll(dir, 0777)
	err = ioutil.WriteFile(filename, []byte(html), 0777)
	if err != nil {
		panic(err)
	}
}

func main() {
	aboutPage()
}
