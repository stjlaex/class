<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>
<!-- xslt for reporting comments  -->

<xsl:output method='html' />

<xsl:template match='text()' />


<xsl:template match="html/body/div">
  <xsl:apply-templates select="student"/>
</xsl:template>


<xsl:template match="student">

  <div class="studenthead">
	<div style="background-color:#fff;">
	  <label>&#160;</label>
	  Progress Report
	  <label>
		<xsl:value-of select="comments/startdate/value/text()" />&#160;
		<xsl:value-of select="comments/enddate/value/text()" />&#160;
	  </label>
	</div>
	<div>
	  <label>Student</label>
	  <xsl:value-of select="displayfullname/value/text()" />&#160;
	</div>
	<div>
	  <label>Form</label>
	  <xsl:value-of select="registrationgroup/value/text()" />
	</div>
  </div>

	<div class='spacer'></div>

 	<xsl:variable name="catname" select="comments/cattable/category/name"/>
 	<xsl:variable name="comment" select="comments/comment"/>
	<xsl:variable name="subject" select="comments/subtable/subject"/>

	  <table>
		<tr>
		  <td>
		  </td>
		  <xsl:for-each select="$catname">
			<th style="width:15%;">
			  <xsl:value-of select="."/>
			</th>
		  </xsl:for-each>
		</tr>

		<xsl:for-each select="$subject">
		  <xsl:variable name="currentsubid" select="value_db"/>
		  <tr>
			<th>
			  <xsl:value-of select="value"/>
			</th>
			<xsl:for-each select="$catname">
			  <xsl:variable name="currentcatname" select="."/>
			  <td>
				<xsl:variable name="catrating">
				  <xsl:value-of select="sum($comment[subject/value_db=$currentsubid]/categories/category[label=$currentcatname]/rating/value)" />
				</xsl:variable>
			  <xsl:choose>
				<xsl:when test="$catrating&lt;0">
				  <div>Poor</div>
				</xsl:when>
				<xsl:when test="$catrating&gt;0">
				  <div>Good</div>
				</xsl:when>
				<xsl:otherwise>
				  <div>-</div>
				</xsl:otherwise>
			  </xsl:choose>
			  </td>
			</xsl:for-each>
		  </tr>
		</xsl:for-each>

	  </table>

	  <xsl:for-each select="$catname">
		<xsl:variable name="currentcatname" select="."/>
		<xsl:if test="$comment[categories/category/label=$currentcatname]" >
		  <div class="studenthead">
			<div>
			  <label><xsl:value-of select="."/></label>
			</div>
		  </div>
		  <xsl:apply-templates select="$comment[categories/category/label=$currentcatname]">
			<xsl:sort select="subject/value/text()" order="ascending" case-order="upper-first" />
		  </xsl:apply-templates>
		</xsl:if>
	  </xsl:for-each>


<hr />

</xsl:template>

<xsl:template match="comment">
<div class="summary">
	<label>
	  <xsl:value-of select="subject/value/text()" />
	  <xsl:text>&#160;-&#160;</xsl:text>
	  <xsl:value-of select="teacher/value/text()" />
	</label>
  <p class="comment-text">
	<xsl:value-of select="detail/value/text()" />
  </p>
</div>
</xsl:template>

</xsl:stylesheet>
