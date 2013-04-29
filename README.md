plovr Maven Repository
================

This is a maven repository for plovr. Simply add to your maven repository to use. More configuration to come.

TODOs
* show more example usages
* convert PHP script to something more dependable

Maven
-----

Include our github repository in your pom.xml:
```xml
<repository>
  <id>org.plovr.github.hikirsch</id>
  <name>plovr Maven Repository via GitHub</name>
  <url>http://hikirsch.github.io/plovr-mvn-repo</url>
</repository>
```
Add dependency:
```xml
<dependency>
   <groupId>org.plovr</groupId>
   <artifactId>plovr</artifactId>
   <version>eba786b34df9</version>
</dependency>
```
